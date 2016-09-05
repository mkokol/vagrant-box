<?php

class PartnershipController extends Helpers_General_ControllerAction
{

    /******************************************************************************************************************
     * Global partners pages like partner home page and rules page
     *****************************************************************************************************************/

    /**
     * Show main partner page
     * All content in this page content is loaded with ajax
     */
    public function indexAction()
    {
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation('user_menu');
        $this->appendStylesheet('pages/partnership_index');
        $this->appendFile('partnership/index');
        $this->appendWidgets('tablesorter');
        $this->view->acceptShopRules = Zend_Auth::getInstance()->getIdentity()->accept_shop_rules;
    }

    /**
     * Display for partners rules for cooperation
     * Called By AJAX
     */
    public function infoAction()
    {
        $this->loadTranslation(array('user_menu', 'partnership/info'));
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->view->acceptShopRules = Zend_Auth::getInstance()->getIdentity()->accept_shop_rules;
    }


    /**
     * Display for partner all rules for cooperation and allow to accept them
     * Static page
     */
    public function shoprulesAction()
    {
        $this->loadTranslation('partnership/shoprules');
        $this->appendFile('partnership/shoprules');
    }

    /**
     * Called when user is accepting a partner rules
     * Called By AJAX
     */
    public function acceptrulesAction()
    {
        $this->removeDefaultView();
        $users = new Users();
        $users->acceptUserShopRules();
        $this->sendAdminNewPartnerRegistration('Коколюс Михайло', 'mickokolius@gmail.com');
        $this->viewJson(array('status' => 'success'));
    }

    /**
     * Display home partner page
     * Called By AJAX
     */
    public function typesAction()
    {
        $this->loadTranslation('partnership/types');
        $this->view->acceptShopRules = Zend_Auth::getInstance()->getIdentity()->accept_shop_rules;
        $productsItems = new ProductsItems();
        $this->view->benefit = $productsItems->getUserOrderedProductsBenefit(
            Users::getCarrentUserId(),
            Zend_Auth::getInstance()->getIdentity()->shop_code
        );
    }

    /******************************************************************************************************************
     * All methods related to referrals program
     *****************************************************************************************************************/

    /**
     * Display referrals partner page
     * Called By AJAX
     */
    public function referralsAction()
    {
        $this->appendWidgets('social');
        $this->loadTranslation('partnership/referrals');
        $this->view->acceptShopRules = Zend_Auth::getInstance()->getIdentity()->accept_shop_rules;
        $this->view->refKey = Zend_Auth::getInstance()->getIdentity()->shop_code;
        $productsItems = new ProductsItems();
        $this->view->benefit = $productsItems->getUserOrderedProductsBenefit(
            Users::getCarrentUserId(),
            Zend_Auth::getInstance()->getIdentity()->shop_code
        );
    }

    /******************************************************************************************************************
     * All methods related to partner products cooperation program
     *****************************************************************************************************************/

    /**
     * Display partners product page
     * Called By AJAX
     */
    public function publicAction()
    {
        $this->loadTranslation(array('products', 'partnership/public'));
        $this->view->acceptShopRules = Zend_Auth::getInstance()->getIdentity()->accept_shop_rules;
        $productsGroup = new ProductsGroup();
        $this->view->publicProductsGroup = $productsGroup->getPublic();
        $productsItems = new ProductsItems();
        $this->view->benefit = $productsItems->getUserOrderedProductsBenefit(
            Users::getCarrentUserId(),
            Zend_Auth::getInstance()->getIdentity()->shop_code
        );
    }

    /**
     * Load partners products
     * Called By AJAX
     */
    public function loadproductsAction()
    {
        $this->loadTranslation(array('products', 'partnership/public', 'produces/controls/imagepaging'));
        $groupId = $this->_request->getQuery('groupId', null);
        $this->view->currentPage = $page = $this->_request->getQuery('page', 1);
        $productsItems = new ProductsItems();
        $userId = Users::getCarrentUserId();
        $this->view->pageCount = $productsItems->getUserProductsPageCount($userId, $groupId);
        $this->view->userProducts = $productsItems->getUserProductsByPage($page, $userId, $groupId);
    }

    /**
     * Show popup window that will ask witch product you would like to create
     */
    public function selectproductsAction()
    {
        $this->loadTranslation(array('products', 'partnership/selectproducts'));
        $productsGroup = new ProductsGroup();
        $this->view->publicProductsGroup = $productsGroup->getPublic();
    }

    /**
     * show create product page
     */
    public function productAction()
    {
        $itemId = $this->_request->getParam('id', 0);
        $defaultGroup = '';

        if ($itemId) {
            $productsItems = new ProductsItems();

            $product = $productsItems->fetchProduct($itemId);

            if ($product !== null && $product['user_id'] != Users::getCarrentUserId()) {
                $this->error404();

                return;
            }

            if ($product !== null) {
                $itemXml = new SimpleXMLElement(stripslashes($product['content_xml']));
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

        $this->appendFile(array('produces/studio', 'partnership/product', "partnership/create/$group"));
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
     * save product to DB
     */
    public function saveproductAction()
    {
        $this->removeDefaultView();

        $xml = $this->_request->getQuery('xml');
        $groupName = $this->_request->getQuery('group');
        $id = $this->_request->getQuery('id', null);

        $groupId = ProductsGroup::getGroupId($groupName);
        $productsItems = new ProductsItems();

        $data = [
            'user_id' => Users::getCarrentUserId(),
            'group_id' => $groupId,
            'status' => 'new',
            'content_xml' => $xml
        ];

        if ($id) {
            $productsItems->update($data, "id = '$id'");
        } else {
            $data['created'] = date('Y-m-d H:i:s');
            $productsItems->insert($data);
        }

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userDate = $auth->getIdentity();
            if ($userDate['permission'] != 'admin') {
                $this->sendAdminPartnerCreatedProduct('Коколюс Михайло', 'mickokolius@gmail.com');
            }
        }

        $this->viewJson(['status' => 'success']);
    }

    /**
     * Delete partners product popup window
     */
    public function deleteproductwndAction()
    {
        $this->loadTranslation('partnership/deleteproductwnd');
        $this->view->productsItemsId = $this->_request->getParam('id');
    }

    /**
     * Delete partners product from system
     */
    public function deleteproductAction()
    {
        $this->removeDefaultView();
        if ($this->_request->isPost()) {
            $productsItems = new ProductsItems();
            $productsItems->delete('id = ' . $this->_request->getParam('id'));
        }
        $this->viewJson(array('status' => 'success'));
    }

    /******************************************************************************************************************
     * All methods related to partners balance
     *****************************************************************************************************************/
    /**
     * Display partners balance statistic page.
     * Called By AJAX
     */
    public function balanceAction()
    {
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation(array('partnership/balance', 'user_menu', 'products'));
        $productsItems = new ProductsItems();
        $this->view->productsGrouped = $productsItems->getUserOrderedProducts(
            Users::getCarrentUserId(),
            Zend_Auth::getInstance()->getIdentity()->shop_code
        );
        $this->view->benefit = $productsItems->getUserOrderedProductsBenefit(
            Users::getCarrentUserId(),
            Zend_Auth::getInstance()->getIdentity()->shop_code
        );
        $usersPayOut = new UsersPayOut();
        $this->view->payOutHistory = $usersPayOut->getUserShortHistory(Users::getCarrentUserId());
    }

    /**
     * Show popup window for making pay out for partner
     */
    public function payoutwndAction()
    {
        $this->loadTranslation('partnership/payoutwnd');
    }

    /**
     * Add pay out in list
     */
    public function payoutAction()
    {
        $this->removeDefaultView();
        $productsItems = new ProductsItems();
        $productsGrouped = $productsItems->getUserOrderedProducts(
            Users::getCarrentUserId(),
            Zend_Auth::getInstance()->getIdentity()->shop_code
        );
        if ($productsGrouped['benefitDone'] >= UsersPayOut::$minSum) {
            $refOrder = array();
            $myItem = array();
            foreach ($productsGrouped['data'] as $order) {
                if (($order['status'] == 'closed') || ($order['status'] == 'canceled')) {
                    if (($order['shop_code'] == Zend_Auth::getInstance()->getIdentity()->shop_code) && (!in_array($order['id'], $refOrder))) {
                        $refOrder[] = $order['id'];
                    }
                    foreach ($order['items'] as $item) {
                        if ($item['basket_item_id'] && $item['byprod'] != '0%') {
                            $myItem[] = $item['basket_item_id'];
                        }
                    }
                }
            }
            $usersPayOut = new UsersPayOut();
            $usersPayOutId = $usersPayOut->insert(
                array(
                    'user_id' => Users::getCarrentUserId(),
                    'card_number' => $this->_request->getParam('cardNumber', ''),
                    'card_owner' => $this->_request->getParam('cardOwner', ''),
                    'sum' => $productsGrouped['benefitDone'],
                    'status' => 'created',
                    'created' => date('Y-m-d H:i:s')
                )
            );
            if (count($refOrder) > 0) {
                $orders = new Orders();
                $orders->update(
                    array('users_pay_out_id' => $usersPayOutId),
                    'id IN (' . implode(',', $refOrder) . ')'
                );
            }
            if (count($myItem) > 0) {
                $baskets = new Baskets();
                $baskets->update(
                    array('users_pay_out_id' => $usersPayOutId),
                    'id IN (' . implode(',', $myItem) . ')'
                );
            }
            $usersPayOut->update(
                array('status' => 'confirmed'),
                "id = '$usersPayOutId'"
            );
            $this->viewJson(array('status' => 'success'));
        } else {
            $this->viewJson(array('status' => 'error'));
        }
    }
}
