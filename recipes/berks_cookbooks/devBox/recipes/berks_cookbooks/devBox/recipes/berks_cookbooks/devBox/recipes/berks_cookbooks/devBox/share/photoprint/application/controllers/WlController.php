<?php

class WlController extends Helpers_General_ControllerAction
{

    /**
     * View WL sites
     */
    public function indexAction()
    {
        $this->appendWidgets('tablesorter');
        $this->loadTranslation('wl/index');
        $wls = new Wls();
        $this->view->wlList = $wls->getWlList(Users::getCarrentUserId());
    }

    /**
     * View created products page
     */
    public function productsAction()
    {
        $this->appendFile('wl/products');
        $this->loadTranslation('products');
        $wlCode = $this->_request->getParam('code', null);
        $wls = new Wls();
        $this->view->wl = $wls->getWlByCode($wlCode);
        $productsGroup = new ProductsGroup();
        $this->view->publicProductsGroup = $productsGroup->getPublic(true);
    }

    /**
     * Load products list
     */
    public function loadproductsAction()
    {
        $this->loadTranslation(array('products', 'partnership/public', 'produces/controls/imagepaging'));
        $this->view->currentPage = $page = $this->_request->getQuery('page', 1);
        $wlCode = $this->_request->getParam('code', null);
        $group = $this->_request->getQuery('groupId', '');
        $this->view->wlCode = $wlCode;
        $wls = new Wls();
        $wlInfo = $wls->getWlByCode($wlCode);
        $this->view->wl = $wlInfo;
        $wlItems = new WlsItems();
        $this->view->pageCount = $wlItems->getWlProductsPageCount($wlInfo['id'], $group);
        $this->view->userProducts = $wlItems->getWLProductsByPage($wlInfo['id'], $group, $page);
    }

    /**
     * Preview wl created products
     */
    public function previewitemAction()
    {
        $this->loadTranslation("wl/previewitem");
        $itemId = $this->_request->getQuery('id', 0);
        $item = $this->_request->getQuery('item', null);
        if ($item != null) {
            $this->view->item = $item;
        }
        if ($itemId) {
            $wlsItems = new WlsItems();
            $productsItem = $wlsItems->fetchRow('id = ' . $itemId)->toArray();
            $itemXml = new SimpleXMLElement(stripslashes($productsItem['content_xml']));
            if ($itemXml !== false) {
                $this->view->itemXml = $itemXml;
                $this->view->group = $group = (string)$itemXml->group;
                if (($group == 'mousepad') && ($item == 'circle')) {
                    $this->view->template = $item;
                } else {
                    $this->view->template = (string)$itemXml->template;
                }
            }
        }
    }

    /**
     * Select which product user would like to create
     */
    public function selectproductsAction()
    {
        $this->loadTranslation(array('products', 'partnership/selectproducts'));
        $productsGroup = new ProductsGroup();
        $this->view->publicProductsGroup = $productsGroup->getPublic(true);
        $this->view->wlCode = $this->_request->getParam('code', null);
    }

    /**
     * create/edit wl products page
     */
    public function createAction()
    {
        $wlCode = $this->_request->getParam('code', null);
        $this->view->wlCode = $wlCode;
        $itemId = $this->_request->getParam('id', 0);
        $defaultGroup = '';
        if ($itemId) {
            $wls = new Wls();
            $wlInfo = $wls->getWlByCode($wlCode);
            $wlsItems = new WlsItems();
            $itemXml = $wlsItems->getWlProduct($itemId, $wlInfo['id']);
            if ($itemXml !== null) {
                $defaultGroup = (string)$itemXml->group;
                $this->view->itemId = $itemId;
                $this->view->itemXml = $itemXml;
                $this->view->templateId = (string)$itemXml->template;
            }
        }

        $group = $this->_request->getParam('group', $defaultGroup);
        $this->view->group = $group;

        if ($group == 'postcard') {
            $this->view->displayRteEditor = true;
            $this->appendWidgets('texteditor');
        }

        $this->appendFile(array('produces/studio', 'wl/create', 'wl/create/' . $group));
        $this->appendWidgets('jscrollpane');
        $this->loadTranslation(array("produces/$group", 'produces/controls/imagepaging', 'produces/controls/themesgeneral', 'produces/controls/button'));

        $productsTemplates = new ProductsTemplates();
        $groupId = ProductsGroup::getGroupId($group);
        $this->view->productsTemplatesList = $productsTemplates->fetchAll("group_id = '$groupId'", 'order ASC')->toArray();

        $this->view->colors = ProductsItems::$tShirtColors;

        //TODO clean
        $images = new UsersImages();
        $this->view->currentPage = 1;
        $userId = Users::getCarrentUserId();
        $hasNotSortedImages = $images->hasNotSortedImages($userId);
        $this->view->hasNotSortedImages = $hasNotSortedImages;
        $albumId = null;
        if (!$hasNotSortedImages && Zend_Auth::getInstance()->hasIdentity()) {
            $userAlbums = new Albums();
            $album = $userAlbums->getDefoultUuserAlbums(Users::getCarrentUserId());
            $albumId = isset($album['id']) ? $album['id'] : null;
            $this->view->albumTitle = isset($album['title']) ? $album['title'] : '';
        }
        $this->view->albumId = (int)$albumId;
        $this->view->pageCount = $images->getAlbumPageCount($albumId);
        $this->view->imagesList = $images->getAlbumImagesByPage($albumId);
    }

    /**
     * add theme popup window for partners new product
     */
    public function addthemeAction()
    {
        $this->loadTranslation('partnership/addtheme');
        $wlsThemes = new WlsThemes();
        $this->view->wlThemes = $wlsThemes->getThemesByWLCode($this->_request->getParam('code', null));
    }

    /**
     * save product to DB
     */
    public function saveproductAction()
    {
        $this->removeDefaultView();
        $xml = $this->_request->getQuery('xml');
        $groupId = ProductsGroup::getGroupId($this->_request->getQuery('group'));
        $id = $this->_request->getQuery('id', null);
        $themeId = $this->_request->getQuery('themeId', null);
        $templateId = $this->_request->getQuery('templateId', null);
        $wlCode = $this->_request->getParam('code', null);
        $wls = new Wls();
        $wlInfo = $wls->getWlByCode($wlCode);
        if (!$themeId) {
            $newTheme = $this->_request->getQuery('newTheme', 'none');
            $wlsThemes = new WlsThemes();
            $themeId = $wlsThemes->insert(
                array(
                    'group_id' => $groupId
                , 'wl_id' => $wlInfo['id']
                , 'name' => $newTheme
                , 'ua' => $newTheme
                , 'ru' => $newTheme
                )
            );
        }
        $wlsItems = new WlsItems();
        $data = array(
            'user_id' => Users::getCarrentUserId()
        , 'group_id' => $groupId
        , 'theme_id' => $themeId
        , 'template_id' => $templateId
        , 'wl_id' => $wlInfo['id']
        , 'status' => 'public'
        , 'content_xml' => $xml
        );
        if ($id) {
            $wlsItems->update($data, "id = '$id'");
        } else {
            $data['created'] = date('Y-m-d H:i:s');
            $wlsItems->insert($data);
        }
        $result = array('status' => 'success');
        $this->viewJson($result);
    }

    /**
     * Show delete white label product
     */
    public function deleteproductwndAction()
    {
        $this->loadTranslation('wl/deleteproductwnd');
        $this->view->productsItemsId = $this->_request->getParam('id');
    }

    /**
     * Delete white lavel product
     */
    public function deleteproductAction()
    {
        $this->removeDefaultView();
        if ($this->_request->isPost()) {
            $wlsItems = new WlsItems();
            $wlsItems->update(
                array(
                    'status' => 'deleted'
                ),
                'id = ' . $this->_request->getParam('id')
            );
        }
        $this->viewJson(array('status' => 'success'));
    }


    public function statisticAction()
    {
        $wlCode = $this->_request->getParam('code', null);
        $page = (int)$this->_request->getQuery('page', 1);
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->appendWidgets('tablesorter');
        $this->loadTranslation(array('partnership/balance', 'user_menu', 'products', 'produces/controls/imagepaging'));
        $wls = new Wls();
        $wl = $wls->getWlByCode($wlCode);
        $orders = new Orders();
        $orderList = $orders->getWLOrdersByPage($wl['id'], $page);
        $this->view->pageCount = $orders->getWLOrdersPageCount($wl['id']);
        $this->view->currentPage = $page;
        $this->view->pageBaseUrl = "/wl/statistic/code/{$wlCode}";
        $ordersId = array();

        foreach ($orderList as $key => $order) {
            $ordersId[] = $order['id'];
            $orderList[$key]['payment'] = 0;
            $orderList[$key]['items'] = array();
        }

        $statistic = array('closed' => 0, 'pending' => 0, 'canceled' => 0);

        if (count($ordersId)) {
            $products = new Products();
            $productsPrices = $products->getProductsPrices();
            $baskets = new Baskets();
            $basketItems = $baskets->getOrdersItems($ordersId);

            foreach ($basketItems as $item) {
                $orderedItem = $item;
                $itemXml = new SimpleXMLElement(stripslashes($orderedItem['dataXml']));
                $orderedItem['item'] = (string)$itemXml->item;
                $templateId = (string)$itemXml->template;
                $itemPrice = (isset($productsPrices[$orderedItem['item'] . $templateId]))
                    ? $productsPrices[$orderedItem['item'] . $templateId]
                    : $productsPrices[$orderedItem['item']];
                $orderedItem['price'] = $itemPrice * $item['count'];
                $orderList[$item['order_id']]['items'][] = $orderedItem;
                $orderList[$item['order_id']]['payment'] += $orderedItem['price'];

                if ($item['status'] == 'closed') {
                    $statistic['closed'] += $orderedItem['price'] / 100 * Wls::$benefit;
                } elseif ($item['status'] == 'canceled') {
                    $statistic['canceled'] += $orderedItem['price'] / 100 * Wls::$benefit;
                } else {
                    $statistic['pending'] += $orderedItem['price'] / 100 * Wls::$benefit;
                }
            }
        }
        $this->view->statistic = $statistic;
        $this->view->orderList = $orderList;
    }

}