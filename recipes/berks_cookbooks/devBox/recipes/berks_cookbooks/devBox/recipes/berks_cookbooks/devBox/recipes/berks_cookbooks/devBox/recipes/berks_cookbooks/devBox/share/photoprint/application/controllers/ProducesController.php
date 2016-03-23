<?php

class ProducesController extends Helpers_General_ControllerAction
{

    public function indexAction()
    {
        $this->error404();
    }

    /**
     * view public products by group
     */
    public function productsAction()
    {
        $group = $this->_request->getParam('group', '');

        $this->validateProductGroup($group);
        $this->addBreadcrumbsLevel($group);

        $this->loadTranslation(['products', 'produces/products', "produces/products/{$group}"]);

        $this->view->group = $group;
        $productsGroup = new ProductsGroup();
        $this->view->publicProductsGroup = $productsGroup->getPublic();
        $products = new Products();
        $this->view->publicProductsDatails = $products->getProductsDetails($group);
    }

    /**
     * pop up preview product photos
     */
    public function productpreviewAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            $this->error404();
        }

        $this->loadTranslation('products');
        $this->view->item = $item = $this->_request->getParam('item', '');
        $products = new Products();
        $this->view->group = $products->fetchGroupByProductName($item);
    }

    /**
     * view product catalog and product view item
     */
    public function catalogAction()
    {
        // Seo old style support
        if (null !== $this->_request->getQuery('page', null)) {
            $item = $this->_request->getParam('item', '');
            $themeId = $this->_request->getQuery('themeId', '');
            $pageBaseUrlParams = ($themeId != null) ? "?themeId={$themeId}" : '';

            $this->redirectTo(
                "produces/catalog/item/{$item}{$pageBaseUrlParams}",
                ['code' => 301]
            );
        }

        $item = $this->_request->getParam('item', '');
        $id = $this->_request->getQuery('id', '');

        $products = new Products();
        $group = $products->fetchGroupByProductName($item);

        $this->addBreadcrumbsLevel($group, '/produces/products/group/' . $group);

        $this->validateProductGroup($group);
        $this->loadTranslation(['products', 'produces/catalog']);
        $this->appendFile('produces/catalog');

        $productsThemes = new ProductsThemes();
        $productsThemesList = $productsThemes->getItemsThemes($item);

        $this->view->id = $id;
        $this->view->item = $item;
        $this->view->group = $group;
        $this->view->productsThemes = $productsThemesList;

        if ($id) {
            $this->loadViewProduct($group, $productsThemesList);
        } else {
            $this->loadCatalogProducts($group, $productsThemesList);
        }

        if ($this->isEditSiteSettingsOn()) {
            $this->loadTranslation('seo_url_tmp');
            $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
            $defaultUrl = '/' . $language . '/produces/catalog/item/' . $item;
            $seoUrlParams = [
                'controller'  => 'produces', 'action' => 'catalog',
                'param1_name' => 'item', 'param1_val' => $item, 'param2_name' => '', 'param2_val' => '',
                'get1_name'   => '', 'get1_val' => '', 'get2_name' => '', 'get2_val' => ''
            ];

            if ($themeId = $this->_request->getQuery('themeId', null)) {
                $seoUrlParams['get1_name'] = 'themeId';
                $seoUrlParams['get1_val'] = $themeId;
                $defaultUrl .= '?themeId=' . $themeId;
            }

            if ($subThemeId = $this->_request->getQuery('tagId', null)) {
                $seoUrlParams['get2_name'] = 'tagId';
                $seoUrlParams['get2_val'] = $subThemeId;
                $defaultUrl .= '?tagId=' . $subThemeId;
            }

            if ($productId = $this->_request->getQuery('id', null)) {
                $seoUrlParams['get1_name'] = 'id';
                $seoUrlParams['get1_val'] = $productId;
                $defaultUrl .= '?id=' . $productId;
            }

            $seoRoute = new SeoRoute();
            $seoUrlParams['lang'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
            $seoRouteRecord = $seoRoute->fetchSEORecord($seoUrlParams);

            if (!$seoRouteRecord) {
                $seoRouteRecord = $seoUrlParams;
                $seoRouteRecord['id'] = null;
                $seoRouteRecord['url'] = $defaultUrl;
                $seoRouteRecord['seo_url'] = '';
            }

            $this->view->seoRouteRecord = $seoRouteRecord;

            if ($seoRouteRecord['id']) {
                $seoContents = new SeoContents();
                $this->view->seoContent = $seoContents->fetchRow('seo_route_id = ' . $seoRouteRecord['id']);
            }

            if ($seoRouteRecord['seo_url']) {
                $this->view->seoRouteRedirects = $seoRoute
                    ->fetchAll("seo_url = '{$seoRouteRecord['seo_url']}' AND type = 'redirect'")->toArray();
            }
        }
    }

    /**
     * Load more products with AJAX
     */
    public function catalogProductsAction()
    {
        $item = $this->_request->getParam('item', '');

        $products = new Products();
        $group = $products->fetchGroupByProductName($item);

        $productsThemes = new ProductsThemes();
        $productsThemesList = $productsThemes->getItemsThemes($item);

        $this->loadCatalogProducts($group, $productsThemesList);
    }

    /**
     * Function for loading catalog page
     *
     * @param $group
     * @param null $productsThemesList
     */
    private function loadCatalogProducts($group, $productsThemesList)
    {
        $this->view->currentThemeId = $themeId = $this->_request->getQuery('themeId', null);
        $this->view->currentSubThemeId = $subThemeId = $this->_request->getQuery('tagId', null);
        $this->view->currentPage = $page = (int)$this->_request->getQuery('page', 1);
        $this->loadTranslation(['products', 'produces/catalog', 'produces/products', 'produces/controls/imagepaging']);

        $themeIdTranslation = ($themeId === null) ? '0' : $themeId;
        $subThemeIdTranslation = ($subThemeId === null) ? '0' : $subThemeId;
        $this->view->item = $item = $this->_request->getParam('item', '');
        if (file_exists(BASE_PATH . "/application/languages/produces/catalog/{$item}/{$themeIdTranslation}-{$subThemeIdTranslation}.xml")) {
            $this->loadTranslation("produces/catalog/{$item}/{$themeIdTranslation}-{$subThemeIdTranslation}");
        }

        $productsTags = new ProductsTags();
        $themesTags = $productsTags->getThemesTags($item, $themeId);
        $this->view->themesTags = $themesTags;

        $pageBaseUrl = "/produces/catalog/item/$item";
        $pageBaseUrlParams = ($themeId != null) ? ['themeId' => $themeId] : [];
        if ($themeId == null) {
            $seoRoute = new SeoRoute();
            $seoRouteRecord = $seoRoute->fetchSEORecord([
                'lang'        => $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang'),
                'controller'  => 'produces', 'action' => 'catalog',
                'param1_name' => 'item', 'param1_val' => $item, 'param2_name' => '', 'param2_val' => '',
                'get1_name'   => '', 'get1_val' => '', 'get2_name' => '', 'get2_val' => ''
            ]);

            if (isset($seoRouteRecord['seo_url'])) {
                $pageBaseUrl = '/' . preg_replace('/^\/(ru|ua)\//', '', $seoRouteRecord['seo_url'], 1);
                $pageBaseUrlParams = [];
            }

            $this->addBreadcrumbsLevel($item, '/produces/catalog/item/' . $item);
        }

        $t = Helpers_General_ControllerAction::getLoadedTranslation();
        $seoTitle = sprintf(
            $t->_('seo_theme_prefix_title'),
            $t->_("seo_{$item}")
        );
        $seoDescription = sprintf(
            $t->_('seo_theme_prefix_description'),
            $t->_("seo_{$item}"),
            $t->_("seo_{$group}")
        );
        $seoHeader = $t->_($item) . '.';

        if ($themeId && $productsThemesList) {
            foreach ($productsThemesList as $theme) {
                if ($theme['id'] == $themeId) {
                    $language = Helpers_General_UrlManager::getLanguage();
                    $subThemeIdName = '';

                    $themeUrl = ($theme['seo_url'])
                        ? $theme['seo_url']
                        : "/produces/catalog/item/{$item}?themeId={$theme['id']}";
                    $this->addBreadcrumbsLevel($theme[$language], $themeUrl);

                    if ($theme['seo_url']) {
                        $pageBaseUrl = '/' . preg_replace('/^\/(ru|ua)\//', '', $theme['seo_url'], 1);
                        $pageBaseUrlParams = [];
                    }

                    if ($themesTags) {
                        foreach ($themesTags as $tag) {
                            if ($subThemeId == $tag['id'] && $tag["$language"]) {
                                $this->addBreadcrumbsLevel($tag[$language]);

                                $subThemeIdName = $tag["$language"];
                                if ($tag['seo_url']) {
                                    $pageBaseUrl = '/' . preg_replace('/^\/(ru|ua)\//', '', $tag['seo_url'], 1);
                                    $pageBaseUrlParams = [];
                                }
                            }
                        }
                    }

                    // SEO setting title and description
                    if ($theme["$language"]) {
                        $seoThemeTitle = $theme["$language"];
                        if ($subThemeIdName) {
                            $seoThemeTitle .= ', ' . $subThemeIdName;
                        }
                        $seoTitle .= sprintf(
                            $t->_('seo_theme_suffix_title'),
                            $seoThemeTitle,
                            $t->_("seo_{$group}")
                        );
                        $seoDescription .= ' ' . sprintf(
                                $t->_('seo_theme_suffix_description'),
                                $seoThemeTitle,
                                $t->_("seo_{$group}")
                            );
                        $seoHeader .= ' ' . sprintf(
                                $t->_('seo_theme_suffix_header'),
                                $t->_("{$group}"),
                                $seoThemeTitle
                            );
                    }
                }
            }
        }

        if ($page != 1 && $page != '') {
            $seoTitle .= ' ' . sprintf($t->_('seo_page'), $page);
            $seoDescription .= ' ' . sprintf($t->_('seo_page'), $page);
            $seoHeader .= ' ' . sprintf($t->_('seo_page'), $page);
        }
        $this->view->title = $seoTitle;
        $this->view->description = $seoDescription;
        $this->view->header = $seoHeader;

        $this->view->pageBaseUrl = $pageBaseUrl;
        $this->view->pageBaseUrlParams = $pageBaseUrlParams;

        $productsItems = new ProductsItems();
        $this->view->pageCount = $productsItems->getProductsPageCount($group, $themeId, $subThemeId);

        $productList = $productsItems->getProductsByPage($group, $item, $themeId, $subThemeId, $page);

        if (!count($productList)) {
            $this->error404();

            return;
        }

        foreach ($productList as $key => $product) {
            $productList[$key]['content_xml'] = new SimpleXMLElement(stripslashes($product['content_xml']));
        }
        $this->view->productList = $productList;
    }

    private function loadViewProduct($group, $productsThemesList)
    {
        $this->appendFile(['produces/view', 'produces/studio']);
        $this->loadTranslation(['products', 'produces/catalog', 'produces/products', 'produces/view', 'produces/view-sm']);

        $this->view->id = $itemId = $this->_request->getQuery('id', 0);
        $this->view->item = $item = $this->_request->getParam('item', '');

        if (file_exists(BASE_PATH . "/application/languages/produces/catalog-item/{$item}/{$itemId}.xml")) {
            $this->loadTranslation("produces/catalog-item/{$item}/{$itemId}");
        }

        if ($itemId) {
            $productsItems = new ProductsItems();
            $productsItem = $productsItems->fetchProduct($itemId);

            if ($productsItem === null) {
                $this->error404();

                return;
            }
            $this->view->product = $productsItem;
            $language = Helpers_General_UrlManager::getLanguage();
            $t = Helpers_General_ControllerAction::getLoadedTranslation();
            $this->view->title = sprintf(
                $t->_('seo_item_title'),
                $t->_("seo_{$item}"),
                $t->_("seo_{$group}"),
                $productsItem[$language]
            );
            $this->view->description = sprintf(
                $t->_('seo_item_description'),
                $t->_("seo_{$group}"),
                $t->_($group),
                $productsItem[$language],
                $t->_("seo_{$item}")
            );
            $user = new Users();
            $this->view->userInfo = $user->getInfoById($productsItem['user_id']);
            $this->view->userProductsCount = $productsItems->getUserProductsCount($productsItem['user_id']);
            $itemXml = $productsItem['content_xml'];
            if ($itemXml !== false) {
                $this->view->currentThemeId = $currentThemeId = (int)$productsItem['products_themes_id'];
                $this->view->currentSubThemeId = $currentTagId = (int)$productsItem['products_tags_id'];

                $selectedTheme = array_filter($productsThemesList, function ($theme) use ($currentThemeId) {
                    return ($theme['id'] == $currentThemeId);
                });
                $this->addBreadcrumbsLevelFromArray($selectedTheme, $item);

                $productsTags = new ProductsTags();
                $themesTags = $productsTags->getThemesTags($item, $currentThemeId);

                foreach ($themesTags as $tag) {
                    if ($currentTagId == $tag['id'] && $tag["$language"]) {
                        $tagUrl = ($tag['seo_url'])
                            ? $tag['seo_url']
                            : "/produces/catalog/item/{$item}?themeId={$currentThemeId}&tagId={$tag['id']}";
                        $this->addBreadcrumbsLevel($tag[$language], $tagUrl);
                    }
                }

                $this->view->productsItem = $productsItem;
                $this->view->itemXml = $itemXml;
                $this->view->group = $group = (string)$itemXml->group;
                $this->view->template = (($group == 'mousepad') && ($item == 'circle')) ? $item : (string)$itemXml->template;

                $this->addBreadcrumbsLevel($productsItem[$language]);

                $similarProducts = $productsItems->getSimilarProducts($item, $itemId, $currentThemeId, $currentTagId);
                $similarProducts = $this->parseXmlListToObject($similarProducts);
                $this->view->similarProducts = $similarProducts;
            }
            $this->view->colors = ProductsItems::$tShirtColors;
            $this->view->main_img = true;
        }
    }

    public function warrantyAction()
    {
        $this->removeDefaultView();
        $this->loadTranslation('produces/warranty');
        $this->view->t = self::getLoadedTranslation();
        $result['html'] = $this->view->render('produces/warranty.phtml');
        $this->viewJson($result);
    }

    /**
     * Display popup that shows t-shirt sizes
     */
    public function tShirtSizesAction()
    {
        if (!$this->_request->isXmlHttpRequest()) {
            $this->error404();
        }

        $this->loadTranslation('produces/t-shirt-sizes');
        $this->view->type = $item = $this->_request->getParam('type', '');
    }

    /**
     * create and edit product item
     */
    public function createAction()
    {
        $itemId = $this->_request->getParam('id', 0);
        $upItemId = $this->_request->getParam('upid', 0);
        $this->view->showFullInfo = true;
        $defaultItem = '';
        if ($itemId) {
            $itemXml = $this->getProductFromBasket($itemId);
            if ($itemXml !== false) {
                $defaultItem = (string)$itemXml->item;
                $this->view->itemId = $itemId;
                $this->view->itemXml = $itemXml;
                $this->view->templateId = (string)$itemXml->template;
            }
        } else if ($upItemId) {
            $productsItems = new ProductsItems();
            $productsItem = $productsItems->fetchRow('id = ' . $upItemId)->toArray();
            $itemXml = new SimpleXMLElement(stripslashes($productsItem['content_xml']));
            if ($itemXml !== false) {
                $defaultItem = (string)$itemXml->item;
                $this->view->itemXml = $itemXml;
                $this->view->templateId = (string)$itemXml->template;
            }
        }
        $item = $this->_request->getParam('item', $defaultItem);
        $this->view->item = $item;
        $products = new Products();
        $group = $products->fetchGroupByProductName($item);
        $this->view->group = $group;

        if ($group == 'postcard') {
            $this->view->displayRteEditor = true;
            $this->appendWidgets('texteditor');
        }
        $this->appendFile(['produces/studio', 'produces/create', 'produces/create/' . $group]);
        $this->appendWidgets('jscrollpane');
        $this->loadTranslation(["produces/$group", 'produces/controls/imagepaging', 'produces/controls/themesgeneral', 'produces/controls/button']);

        $productsTemplates = new ProductsTemplates();
        $groupId = ProductsGroup::getGroupId($group);
        $this->view->productsTemplatesList = $productsTemplates->fetchAll("group_id = '$groupId'", 'order ASC')->toArray();

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
        $this->view->colors = ['white'];
    }

    /**
     * preview created products
     */
    public function previewitemAction()
    {
        $this->loadTranslation("produces/previewitem");
        $itemId = $this->_request->getQuery('id', 0);
        $item = $this->_request->getQuery('item', null);
        if ($item != null) {
            $this->view->item = $item;
        }
        if ($itemId) {
            $productsItems = new ProductsItems();
            $productsItem = $productsItems->fetchRow('id = ' . $itemId)->toArray();
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
     * preview products prom basket
     */
    public function reviewitemAction()
    {
        $this->loadTranslation("produces/reviewitem");
        $itemId = $this->_request->getParam('id', 0);
        $this->view->showFullInfo = true;
        if ($itemId) {
            $itemXml = $this->getProductFromBasket($itemId);
            if ($itemXml !== false) {
                $this->view->itemXml = $itemXml;
                $this->view->item = $item = (string)$itemXml->item;
                $color = (string)$itemXml->color;
                $this->view->color = ($color) ? $color : 'white';
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
     * get images for creating products
     */
    public function imageAction()
    {
        $this->removeDefaultView();
        $this->loadTranslation('produces/controls/imagepaging');
        $page = $this->_request->getQuery('page', 1);
        $albumId = $this->_request->getQuery('albumId', null);
        $result = [];
        $images = new UsersImages();
        $this->view->currentPage = $page;
        $this->view->pageCount = $images->getAlbumPageCount($albumId);
        $this->view->t = self::getLoadedTranslation();
        $result['pagingHtml'] = $this->view->render("/controls/ajaxPaging.phtml");
        $result['imagesList'] = $images->getAlbumImagesByPage($albumId, $page);
        $this->viewJson($result);
    }

    public function useralbumAction()
    {
        $userId = Users::getCarrentUserId();
        $albums = new Albums();
        $this->view->allAlbums = $albums->fetchAll('user_id = ' . $userId)->toArray();
        $images = new UsersImages();
        $this->view->hasNotSortedImages = $images->hasNotSortedImages($userId);
    }

    /**
     * Fetching and parsing product XML from DB
     *
     * @param type $elId
     * @return \SimpleXMLElement|boolean
     */
    private function getProductFromBasket($elId)
    {
        if ($elId) {
            $baskets = new Baskets();
            $product = $baskets->fetchRow('id = ' . $elId)->toArray();
            $userId = Users::getCarrentUserId();
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $userData = Zend_Auth::getInstance()->getIdentity();
            }
            if (isset($product['user_id']) && (($product['user_id'] == $userId) || (isset($userData) && ($userData->permission = 'admin')))) {
                $xmlData = new SimpleXMLElement(stripslashes($product['dataXml']));
                if ($xmlData->item) {
                    return $xmlData;
                }
            }
        }

        return false;
    }
}
