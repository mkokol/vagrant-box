<?php

class ApiController extends Helpers_General_ControllerAction
{
    private $wlId = null;
    private $supportedVersions = array(
        'v1.0'
    );

    /**
     * Start point of all api request
     */
    public function publicAction()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');

        $apiVersion = $this->_request->getParam('version');

        if (in_array($apiVersion, $this->supportedVersions) !== true) {
            $this->viewJson(array('message' => 'Our API don\'t support such version'), 404);
            return;
        }

        $wlCode = $this->_request->getParam('wl');
        $wls = new Wls();
        $wlInfo = $wls->getWlByCode($wlCode);
        if ($wlInfo === null) {
            $this->viewJson(array('message' => 'Your partner code is incorrect'), 404);
            return;
        }
        $this->wlId = $wlInfo['id'];

        $method = $this->_request->getParam('method', '');
        if (!$method) {
            $this->viewJson(array('message' => 'Your didn\'t specified a method'), 404);
            return;
        }

        switch ($method) {
            case 'getProducts':
                $this->loadTranslation('wl-products');
                $this->getWlProducts($wlInfo);
                break;
            case 'createOrder':
                $this->createWlOrder($wlInfo);
                break;
            default:
                $this->viewJson(array('message' => "Method '$method' do not exist'"), 404);
        }
    }

    private function getWlProducts($wlInfo)
    {
        $result = array();
        $t = $this->getLoadedTranslation();

        $productsGroup = new ProductsGroup();
        $result['products'] = $productsGroup->getPublicForWL($t);

        $products = new Products();
        $result['items'] = $products->getProductsPricesForWL($t);

        $wlsThemes = new WlsThemes();
        $result['themes'] = $wlsThemes->getThemes($wlInfo['id'], array('id', 'name'));

        $wlsItems = new WlsItems();
        $result['product_items'] = $wlsItems->getAllWlProducts($wlInfo['id']);

        $this->viewJson($result);
    }

    private function createWlOrder()
    {
        $itemCount = $this->_request->getPost('itemCount');
        if($itemCount){
            $orders = new Orders();
            $orderId = $orders->insert(
               array(
                    'user_id' => "wl-{$this->wlId}",
                    'user_name' => $this->_request->getPost('customerName'),
                    'phone' => $this->_request->getPost('customerPhone'),
                    'address' => $this->_request->getPost('customerAddress'),
                    'shop_code' => "wl-{$this->wlId}",
                    'status' => 'created',
                    'date' => date('Y-m-d H:i:s'),
                    'dostavka_else' => $this->_request->getPost('transportattion'),
                    'dostavka_lustivok' => '',
                    'payment_sys' => $this->_request->getPost('payment')
                )
            );

            $products = new Products();
            $productsPrices = $products->getProductsPrices();

            $wlsItems = new WlsItems();
            $payment = 0;
            for ($i = 0; $i < $itemCount; $i++) {
                $itemId = $this->_request->getPost("itemId{$i}");
                $xmlData = $wlsItems->getWlProduct($itemId, $this->wlId);
                $xmlData->item = $this->_request->getPost("item{$i}");
                if($this->_request->getPost("color{$i}")){
                    $xmlData->color = $this->_request->getPost("color{$i}");
                }
                if($this->_request->getPost("size{$i}")){
                    $xmlData->size = $this->_request->getPost("size{$i}");
                }
                $basket = new Baskets();
                $isValid = $basket->validateItem($xmlData);
                if($isValid){
                    $count = (int)$this->_request->getPost("count{$i}");
                    $basket->insert(
                        array(
                            'user_id' => "wl-{$this->wlId}",
                            'product_item_id' => $itemId,
                            'product_group' => $xmlData->group,
                            'status' => 'inOrder',
                            'order_id' => $orderId,
                            'dataXml' => $xmlData->asXML(),
                            'count' => $count,
                            'date' => date('Y-m-d H:i:s')
                        )
                    );
                    $templateId = (string)$xmlData->template;
                    $item = (string)$xmlData->item;
                    $payment += $count * $productsPrices[$item . $templateId];

                }
            }
            $orders->update(
                array( 'payment' =>  $payment),
                "id = $orderId"
            );
        }
        $this->viewJson(
            array(
                'orderId' => $orderId,
                'sum' => $payment
            )
        );
    }
}