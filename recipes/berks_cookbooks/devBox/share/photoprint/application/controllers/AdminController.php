<?php

class AdminController extends Helpers_General_ControllerAction
{
    public function cashProductImagesAction()
    {
        //ru/admin/cash-product-images/item/womantshirt/side/front/color/white
        //ru/admin/cash-product-images/item/mantshirt/side/front/color/white

        //ru/admin/cash-product-images/item/a5/side/main
        //ru/admin/cash-product-images/item/a4/side/main
        //ru/admin/cash-product-images/item/130x180/side/main

        //ru/admin/cash-product-images/item/white/side/left
        //ru/admin/cash-product-images/item/glass/side/left
        //ru/admin/cash-product-images/item/magic/side/left
        //ru/admin/cash-product-images/item/glassmagic/side/left
        //ru/admin/cash-product-images/item/white/side/main
        //ru/admin/cash-product-images/item/glass/side/main
        //ru/admin/cash-product-images/item/magic/side/main
        //ru/admin/cash-product-images/item/glassmagic/side/main

        //ru/admin/cash-product-images/item/cork/side/main
        //ru/admin/cash-product-images/item/ceramic/side/main
        //ru/admin/cash-product-images/item/rubber/side/main

        //ru/admin/cash-product-images/item/square/side/main
        //ru/admin/cash-product-images/item/circle/side/main

        //ru/admin/cash-product-images/item/double/side/main

        $item = strtolower($this->_request->getParam('item', ''));
        $side = strtolower($this->_request->getParam('side', null));
        $color = strtolower($this->_request->getParam('color', null));
        $size = strtolower($this->_request->getParam('size', 'p'));

        $groupId = 0;
        if (in_array($item, ['mantshirt', 'womantshirt'])) {
            $groupId = 6;
        }
        if (in_array($item, ['a5', 'a4', '130x180'])) {
            $groupId = 2;
        }
        if (in_array($item, ['white', 'glass', 'magic', 'glassmagic'])) {
            $groupId = 5;
        }
        if (in_array($item, ['cork', 'ceramic', 'rubber'])) {
            $groupId = 4;
        }
        if (in_array($item, ['square', 'circle'])) {
            $groupId = 3;
        }
        if ($item == 'double') {
            $groupId = 1;
        }

        if (!$groupId) {
            $this->error404();

            return;
        }

        $productItem = new ProductsItems();
        $productList = $productItem->fetchAll('group_id = ' . $groupId)->toArray();

        if (in_array($item, ['white', 'glass', 'magic', 'glassmagic'])) {
            $productListFiltered = [];
            foreach ($productList as $product) {
                $xml = new SimpleXMLElement(stripslashes($product['content_xml']));
                $itemTemplate = (string)$xml->template;
                $productSides = ($itemTemplate == 4) ? ['main'] : ['left', 'right'];
                if (in_array($side, $productSides)) {
                    $productListFiltered[] = $product;
                }
            }
        } else {
            $productListFiltered = $productList;
        }

        $this->view->productList = $productListFiltered;

        $this->view->side = $side;
        $this->view->item = $item;
        $this->view->color = $color;
        $this->view->size = $size;
    }

    public function userAction()
    {
        $id = $this->_request->getQuery('id');
        $this->loadTranslation('admin/user');
        $users = new Users();
        $this->view->user = $users->fetchRow('id = ' . $id);
    }

    /**
     * Product items with prises
     */
    // TODO: rename in menu and link
    public function categoriesAction()
    {
        $this->loadTranslation('user_menu');
        $this->loadTranslation('admin/products');
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->appendFile('admin/categories');
        $this->appendWidgets('tablesorter');
        $products = new Products();
        $productsPrice = $products->getAllProductsPrice();
        $this->view->productsPrice = $productsPrice;
    }

    /**
     * Display all products page
     */
    public function productListAction()
    {
        $page = $this->_request->getQuery('page', 1);
        $userId = $this->_request->getParam('userId', null);

        $this->loadTranslation(['products', 'produces/controls/imagepaging']);
        $this->loadTranslation('user_menu');

        $productsItems = new ProductsItems();

        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->view->userList = $productsItems->getUserList();
        $this->view->currentPage = $page;
        $this->view->pageCount = $productsItems->getUserProductsPageCount($userId);
        $this->view->userProducts = $productsItems->getUserProductsByPage($page, $userId);
        $this->view->pageBaseUrl = '/admin/product-list';
    }

    /**
     * Preview product item pop up
     */
    public function editProductWndAction()
    {
        $id = $this->_request->getParam('id');

        $this->appendFile('lib/jquery.tokenize');
        $this->appendStylesheet('lib/jquery.tokenize');

        $productsItems = new ProductsItems();
        $this->view->product = $productsItems->getProductDetails($id);

        $productsThemes = new ProductsThemes();
        $this->view->productsThemes = $productsThemes->getAllThemes();

        $productsTags = new ProductsTags();
        $this->view->productsTags = $productsTags->getAllTags();

        $this->view->colors = ProductsItems::$tShirtColors;
    }

    /**
     * Save data from product item pop up
     */
    public function editProductAction()
    {
        $id = $this->_request->getParam('id');
        $themeIds = $this->_request->getParam('themeId', null);
        $tagIds = $this->_request->getParam('tagId', null);

        $this->removeDefaultView();

        $productTheme = new ProductsItemsThemes();
        $productTheme->saveProductThemes($id, $themeIds);

        $productTag = new ProductsItemsTags();
        $productTag->saveProductTags($id, $tagIds);

        $productsItems = new ProductsItems();
        $productsItems->update(
            [
                'color'          => $this->_request->getPost('color'),
                'allowed_colors' => $this->_request->getPost('allowedColors'),
                'cost_diff'      => $this->_request->getPost('costDiff'),
                'ua'             => $this->_request->getPost('ua'),
                'ru'             => $this->_request->getPost('ru'),
                'title_ua'       => $this->_request->getPost('title_ua'),
                'title_ru'       => $this->_request->getPost('title_ru'),
                'description_ua' => $this->_request->getPost('description_ua'),
                'description_ru' => $this->_request->getPost('description_ru'),
                'header_ua'      => $this->_request->getParam('header_ua', ''),
                'header_ru'      => $this->_request->getParam('header_ru', ''),
                'text_ua'        => $this->_request->getParam('text_ua', ''),
                'text_ru'        => $this->_request->getParam('text_ru', ''),
                'status'         => $this->_request->getParam('status'),
            ],
            ['id = ?' => $id]
        );

        $this->viewJson(['status' => 'success']);
    }

    public function deleteProductWndAction()
    {
        $this->loadTranslation('partnership/deleteproductwnd');
        $this->view->id = $this->_request->getParam('id');
    }

    public function deleteProductAction()
    {
        $this->removeDefaultView();
        $productsItems = new ProductsItems();
        $productsItems->delete('id = ' . $this->_request->getParam('id'));
        $this->viewJson(['status' => 'success']);
    }

    public function saveProductPositionAction()
    {
        $this->removeDefaultView();
        $productIds = explode(',', $this->_request->getParam('productIds', ''));
        $themeId = $this->_request->getParam('themeId', null);
        $tagId = $this->_request->getParam('tagId', null);

        $productsOrders = new ProductsOrders();
        $productOrders = $productsOrders->fetchProductOrders($productIds, $themeId, $tagId);
        $order = 1;

        foreach ($productIds as $productId) {
            if (isset($productOrders[$productId])) {
                $productsOrders->update(
                    ['order' => $order],
                    'product_id = ' . $productId
                );
            } else {
                $productsOrders->insert([
                    'product_id' => $productId,
                    'theme_id'   => ($themeId) ? $themeId : null,
                    'tag_id'     => ($tagId) ? $tagId : null,
                    'order'      => $order
                ]);
            }

            $order++;
        }

        $this->viewJson(['status' => 'success']);
    }


    // >>>> themes -----------------------

    public function themeListAction()
    {
        $this->loadTranslation(['user_menu', 'admin/themes']);
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->appendWidgets('tablesorter');
        $productsThemes = new ProductsThemes();
        $this->view->allThemes = $productsThemes->getAllThemesWithSubThemes();
    }

    public function themeEditWndAction()
    {
        $this->loadTranslation('admin/themes');

        $themeId = $this->_request->getParam('id', 0);

        if ($themeId) {
            $productsThemes = new ProductsThemes();
            $theme = $productsThemes->fetchRow("id = '$themeId'");
        } else {
            $theme = (object)[
                'id'       => '', 'name' => '', 'ua' => '', 'ru' => '', 'status' => 'new',
                'title_ua' => '', 'title_ru' => '', 'description_ua' => '', 'description_ru' => ''
            ];
        }

        $this->view->theme = $theme;
    }

    public function themeEditAction()
    {
        $this->removeDefaultView();
        if (!$this->_request->isPost()) {
            $this->viewJson(['status' => 'error']);
        }

        $id = $this->_request->getPost('id');
        $data = [
            'name'           => $this->_request->getPost('name'),
            'ua'             => $this->_request->getPost('ua'),
            'ru'             => $this->_request->getPost('ru'),
            'title_ua'       => $this->_request->getPost('title_ua'),
            'title_ru'       => $this->_request->getPost('title_ru'),
            'description_ua' => $this->_request->getPost('description_ua'),
            'description_ru' => $this->_request->getPost('description_ru'),
            'status'         => $this->_request->getPost('status')
        ];

        $productsThemes = new ProductsThemes();

        if ($id) {
            $productsThemes->update($data, 'id = ' . $id);
        } else {
            $productsThemes->insert($data);
        }

        $this->viewJson(['status' => 'success']);
    }

    public function themeDeleteWndAction()
    {
        $productsThemes = new ProductsThemes();
        $themeId = $this->_request->getParam('id', 0);
        $this->view->theme = $productsThemes->fetchRow("id = '$themeId'");
    }

    public function themeDeleteAction()
    {
        $this->removeDefaultView();

        if ($this->_request->isPost()) {
            $productsThemes = new ProductsThemes();
            $productsThemes->delete('id = ' . $this->_request->getParam('id'));
        }

        $this->viewJson(['status' => 'success']);
    }

    // <<<< themes -----------------------
    // >>>> tags -----------------------

    public function tagListAction()
    {
        $this->loadTranslation(['user_menu', 'admin/tags']);
        $this->appendWidgets('tablesorter');

        $this->view->topMenuItems = Users::getUserMenuItems();

        $productsTags = new ProductsTags();
        $this->view->tags = $productsTags->getAllTags();
    }

    public function tagEditWndAction()
    {
        $this->loadTranslation('admin/tags');

        $tagId = $this->_request->getParam('id', 0);

        if ($tagId) {
            $productsTags = new ProductsTags();
            $tag = $productsTags->fetchRow("id = '$tagId'");
        } else {
            $tag = (object)[
                'id'     => '',
                'name'   => '',
                'ua'     => '',
                'ru'     => '',
                'status' => 'new'
            ];
        }

        $this->view->tag = $tag;
    }

    public function tagEditAction()
    {
        $this->removeDefaultView();
        if (!$this->_request->isPost()) {
            $this->viewJson(['status' => 'error']);
        }

        $id = $this->_request->getPost('id');
        $data = [
            'name'   => $this->_request->getPost('name'),
            'ua'     => $this->_request->getPost('ua'),
            'ru'     => $this->_request->getPost('ru'),
            'status' => $this->_request->getPost('status')
        ];

        $productsTags = new ProductsTags();

        if ($id) {
            $productsTags->update($data, 'id = ' . $id);
        } else {
            $productsTags->insert($data);
        }

        $this->viewJson(['status' => 'success']);
    }

    public function tagDeleteWndAction()
    {
        $this->loadTranslation('admin/tags');
        $productsTags = new ProductsTags();
        $tagId = $this->_request->getParam('id', 0);
        $this->view->tag = $productsTags->fetchRow("id = '$tagId'");
    }

    public function tagDeleteAction()
    {
        $this->removeDefaultView();

        if (!$this->_request->isPost()) {
            $this->viewJson(['status' => 'error']);
        }

        $id = $this->_request->getParam('id');
        $productsTags = new ProductsTags();
        $productsTags->delete('id = ' . $id);
        $this->viewJson(['status' => 'success']);
    }

    // <<<< tags -----------------------

    public function setpriceAction()
    {
        $this->removeDefaultView();
        if ($this->_request->getQuery('templateId', '') == '') {
            $products = new Products();
            $products->update(
                ['price' => $this->_request->getQuery('itemPrice')],
                'id = ' . $this->_request->getQuery('itemId')
            );
        } else {
            $productsTemplates = new ProductsTemplates();
            $productsTemplates->update(
                ['price' => ($this->_request->getQuery('itemPrice', '') != '') ? $this->_request->getQuery('itemPrice') : null],
                'id = ' . $this->_request->getQuery('templateId')
            );
        }
        $this->viewJson(['status' => 'success']);
    }

    public function setpublishedAction()
    {
        $this->removeDefaultView();
        $products = new Products();
        $products->update(
            ['published' => $this->_request->getQuery('itemPublished')],
            'id = ' . $this->_request->getQuery('itemId')
        );
        $this->viewJson(['status' => 'success']);
    }

    public function ordersAction()
    {
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation(['user_menu', 'products', 'admin/orders']);
        $this->appendFile('admin/orders');
        $this->appendWidgets('tablesorter');
        $baskets = new Baskets();
        $basketItems = $baskets->getBasketItem(null, 'new');

        foreach ($basketItems as $key => $product) {
            $xmlData = new SimpleXMLElement(stripslashes($product['dataXml']));
            $basketItems[$key]['previewGroup'] = (string)$xmlData->group;
            $basketItems[$key]['previewModel'] = (string)$xmlData->model;
        }

        $this->view->basketItems = $basketItems;
    }

    public function ordersHistoryAction()
    {
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation(['user_menu', 'products', 'admin/orders-history']);
        $this->appendFile('admin/orders');
        $this->appendWidgets('tablesorter');
        $baskets = new Baskets();
        $basketItems = $baskets->getBasketItem(null, 'old');
        foreach ($basketItems as $key => $product) {
            $xmlData = new SimpleXMLElement(stripslashes($product['dataXml']));
            $basketItems[$key]['previewGroup'] = (string)$xmlData->group;
            $basketItems[$key]['previewModel'] = (string)$xmlData->model;
        }
        $this->view->basketItems = $basketItems;
    }

    /**
     * Show all users photo
     */
    public function photoAction()
    {
        $page = $this->_request->getQuery('page', 1);

        $this->loadTranslation(['user_menu', 'produces/controls/imagepaging']);
        $this->appendWidgets('tablesorter');
        $this->appendFile('admin/photo');

        $images = new UsersImages();
        $this->view->currentPage = $page;
        $this->view->pageCount = $images->getAllPhotosCount();
        $this->view->images = $images->getAllPhotosByPage($page);
        $this->view->pageBaseUrl = '/admin/photo';
        $this->view->pageBaseUrlParams = [];
        $this->view->topMenuItems = Users::getUserMenuItems();
    }

    /**
     * Delete file from DB and file system
     */
    public function photodeleteAction()
    {
        $id = $this->_request->getParam('id');
        $imgId = $this->_request->getParam('imgId');
        $imgName = $this->_request->getParam('imgName');
        $this->removeDefaultView();
        if ($this->_request->isPost()) {
            $usersImages = new UsersImages();
            $usersImages->delete("id = '$id'");
            if (!$usersImages->isUsedImg($imgId)) {
                $images = new Images();
                $imgInfo = $images->fetchRow("id = '$imgId'")->toArray();
                if (file_exists(Images::getImagePath('.', $imgName, 'o', $imgInfo['extension']))) {
                    unlink(Images::getImagePath('.', $imgName, 'o', $imgInfo['extension']));
                }
                unlink(Images::getImagePath('.', $imgName, 'p', $imgInfo['extension']));
                unlink(Images::getImagePath('.', $imgName, 's', $imgInfo['extension']));
                $images->delete("id = '$imgId'");
            }
        }
        $this->viewJson(['status' => 'success']);
    }


    public function saveSeoUrlAction()
    {
        $seoRoute = new SeoRoute();
        $newSeoRouteId = $seoRoute->insert(
            [
                'type'        => 'route',
                'seo_url'     => $this->_request->getPost('seo-url', ''),
                'url'         => $this->_request->getPost('url', ''),
                'lang'        => $this->_request->getPost('lang', ''),
                'controller'  => $this->_request->getPost('controller', ''),
                'action'      => $this->_request->getPost('action', ''),
                'param1_name' => $this->_request->getPost('param1-name', ''),
                'param1_val'  => $this->_request->getPost('param1-val', ''),
                'param2_name' => $this->_request->getPost('param2-name', ''),
                'param2_val'  => $this->_request->getPost('param2-val', ''),
                'get1_name'   => $this->_request->getPost('get1-name', ''),
                'get1_val'    => $this->_request->getPost('get1-val', ''),
                'get2_name'   => $this->_request->getPost('get2-name', ''),
                'get2_val'    => $this->_request->getPost('get2-val', '')
            ]
        );

        if ($id = $this->_request->getPost('id')) {
            $lastSeoRout = $seoRoute->fetchRow('id = ' . $id);
            $seoRoute->update(
                [
                    'type'        => 'redirect',
                    'seo_url'     => $this->_request->getPost('seo-url', ''),
                    'url'         => $lastSeoRout->seo_url,
                    'lang'        => $this->_request->getPost('lang', ''),
                    'controller'  => '',
                    'action'      => '',
                    'param1_name' => '',
                    'param1_val'  => '',
                    'param2_name' => '',
                    'param2_val'  => '',
                    'get1_name'   => '',
                    'get1_val'    => '',
                    'get2_name'   => '',
                    'get2_val'    => ''
                ],
                ['id = ?' => $id]
            );
            $seoRoute->update(
                ['seo_url' => $this->_request->getPost('seo-url', '')],
                ['seo_url = ?' => $lastSeoRout->seo_url]
            );

            $seoContents = new SeoContents();
            $seoContents->update(
                ['seo_route_id' => $newSeoRouteId],
                ['seo_route_id = ?' => $id]
            );
        }

        $this->redirect(
            $this->_request->getPost('seo-url', '')
        );
    }

    public function deleteSeoUrlAction()
    {
        $id = $this->_request->getQuery('id');

        $seoRoute = new SeoRoute();
        $currentSeoRout = $seoRoute->fetchRow('id = ' . $id);
        $seoRoute->delete(['id = ?' => $id]);

        $this->redirect(
            $this->_request->getPost($currentSeoRout['url'], '')
        );
    }


    public function saveSeoContentAction()
    {
        $seoRouteRecordId = $this->_request->getPost('seoRouteRecordId');

        $seoContents = new SeoContents();
        $seoContent = $seoContents->fetchRow('seo_route_id = ' . $seoRouteRecordId);

        $data = [
            'seo_route_id' => $seoRouteRecordId,
            'title'        => $this->_request->getPost('page_title', ''),
            'description'  => $this->_request->getPost('page_description', ''),
            'keywords'     => $this->_request->getPost('page_keywords', ''),
            'h1'           => $this->_request->getPost('page_h1', ''),
            'text'         => $this->_request->getPost('page_text', ''),
            'created_on'   => date('Y-m-d H:i:s')
        ];

        if (isset($seoContent['id'])) {
            $seoContents->update($data, ['id = ?' => $seoContent['id']]);
        } else {
            $seoContents->insert($data);
        }

        $userSession = new Zend_Session_Namespace('UserSession');

        if ($userSession->previousUrl) {
            $this->redirect(
                $userSession->previousUrl
            );
        }

        $this->redirectTo('');
    }

    public function postcardAction()
    {
        $this->view->translate = $this->loadTranslation('produces/postcard');
        $this->appendFile('admin/postcard');
        $producesSesionInfo = new Zend_Session_Namespace('ProducesSesionInfo');
        $theme_config = new Zend_Config_Ini('./config/config.ini', 'theme');
        $producesSesionInfo->selectedTheme = $theme_config->postcard;
        $el_id = $this->_request->getParam('id', 0);
        $xmlData = $this->addProductFromBasket($el_id);

        if ($xmlData !== false) {
            $this->view->isTov = true;
            $this->view->tovId = $el_id;
            $mainImg = $xmlData->img[0];
            $mainImg->url = str_replace('/large/', '/real/', str_replace('_large.', '_real.', $mainImg->url));
            $this->view->main = $mainImg;
            $this->view->inner = $xmlData->img[1];
            $this->view->contentText = html_entity_decode($xmlData->content->text);
        } else {
            $this->view->isTov = false;
        }
    }

    private function addProductFromBasket($el_id)
    {
        if ($el_id) {
            $baskets = new Baskets();
            $product = $baskets->fetchRow('id = ' . $el_id)->toArray();
            $userId = Users::getCarrentUserId();

            if (Zend_Auth::getInstance()->hasIdentity()) {
                $userData = Zend_Auth::getInstance()->getIdentity();
            }

            if (isset($product['id_clienta']) && (($product['id_clienta'] == $userId) || (isset($userData) && ($userData->permission = 'admin')))) {
                $xmlData = new SimpleXMLElement(stripslashes($product['dataXml']));

                if ($xmlData->type) {
                    return $xmlData;
                }
            }
        }

        return false;
    }

    public function changeitemstatusAction()
    {
        $this->removeDefaultView();
        $auth = Zend_Auth::getInstance();
        $result = [];

        if ($auth->hasIdentity()) {
            $user_date = $auth->getIdentity();

            if ($user_date->permission === 'admin') {
                $itemStatus = $this->_request->getQuery('itemStatus');
                $itemId = $this->_request->getQuery('itemId');
                $data = ['status' => $itemStatus];
                $basket = new Baskets();
                $basket->update($data, 'id = ' . $itemId);
                $result['status'] = 'success';
            } else {
                $result['status'] = 'error';
                $result['permission'] = $user_date->permission;
                $result['error'] = 'You do not have permission';
            }
        } else {
            $result['status'] = 'error';
            $result['error'] = 'You are not logined';
        }

        $this->viewJson($result);
    }

    public function changeOrderStatusAction()
    {
        $this->removeDefaultView();
        $auth = Zend_Auth::getInstance();
        $result['status'] = 'error';

        if ($auth->hasIdentity()) {
            $userData = $auth->getIdentity();

            if ($userData->permission === 'admin') {
                $orderStatus = $this->_request->getQuery('orderStatus');
                $orderId = $this->_request->getQuery('orderId');
                $orders = new Orders();
                $orders->update(
                    ['status' => $orderStatus],
                    'id = ' . $orderId
                );
                $basket = new Baskets();
                $orderStatus = ($orderStatus != 'created') ? $orderStatus : 'inOrder';
                $basket->update(
                    ['status' => $orderStatus],
                    'order_id = ' . $orderId
                );

                $t = Helpers_General_ControllerAction::getLoadedTranslation();
                $items = $basket->fetchAll('order_id = ' . $orderId)->toArray();

                foreach ($items as $key => $value) {
                    $xmlData = new SimpleXMLElement(stripslashes($value['dataXml']));
                    $items[$key]['name'] = $t->_((string)$xmlData->item);
                }
                $order = $orders->fetchRow('id = ' . $orderId)->toArray();
                $result['order'] = $order;
                $result['items'] = $items;
                $result['order_status'] = $orderStatus;
                $result['status'] = 'success';

                if ($orderStatus == 'closed') {
                    $usersHashes = new UsersHashes();
                    $userHash = md5(uniqid($order['user_id'] . time()));
                    $now = date('Y-m-d H:i:s');
                    $usersHashes->insert([
                        'user_id'    => $order['user_id'],
                        'hash'       => $userHash,
                        'type'       => 'order_review',
                        'data'       => serialize(['orderId' => $orderId]),
                        'status'     => 'new',
                        'updated_on' => $now,
                        'created_on' => $now
                    ]);
                    $users = new Users();
                    $user = $users->fetchRow('id = ' . $order['user_id'])->toArray();
                    $language = ($order['language'] == 'ua') ? 'uk' : 'ru';
                    $this->sendUserOrderReviewRequest($language, $order['user_name'], $user['email'], $userHash);
                }
            } else {
                $result['error'] = 'You don\'t have permission.';
            }
        } else {
            $result['error'] = 'You are not logged in.';
        }

        $this->viewJson($result);
    }

    public function setOrderStatusAction()
    {
        $orderId = $this->_request->getParam('order');
        $status = $this->_request->getParam('status');

        $orders = new Orders();
        $orders->update(
            ['status' => $status],
            'id = ' . $orderId
        );

        $this->redirectTo('admin/orders');
    }

    public function payoutAction()
    {
        $this->loadTranslation('user_menu');
        $this->view->topMenuItems = Users::getUserMenuItems();
        $this->loadTranslation('admin/payout');
        $this->appendWidgets('tablesorter');
        $usersPayOut = new UsersPayOut();
        $this->view->payout = $usersPayOut->getNewPayOuts();
    }

    public function confirmpayoutAction()
    {
        $this->removeDefaultView();
        $usersPayOutId = $this->_request->getParam('id');
        $usersPayOut = new UsersPayOut();
        $usersPayOut->update(
            ['status' => 'payed'],
            "id = '$usersPayOutId'"
        );
        $this->viewJson(['status' => 'success']);
    }

    public function turnOnOffEditSiteSettingsAction()
    {
        $userSession = new Zend_Session_Namespace('UserSession');
        $userSession->editSiteSettings = (bool)$this->_request->getQuery('edit', false);;

        if ($userSession->previousUrl) {
            $this->redirect($userSession->previousUrl);
        }

        $this->redirectTo('');
    }
}
