<?php

class ProductsItems extends Model
{

    public static $_tableName = 'products_items';
    public static $productsOnPage = 12;

    public static $tShirtColors = ['white', 'yellow', 'blue', 'green', 'red', 'black'];
    public static $tShirtAllColors = ['white', 'yellow', 'blue', 'green', 'red', 'black'];

    private static $productsBenefitGrouped = null;
    private static $productsPrices = null;

    public static function getProductItemPrice($productsItem, $defaultItem = '')
    {
        if (isset($productsItem['item_payment']) && $productsItem['item_payment']) {
            return $productsItem['item_payment'];
        }

        if (isset($productsItem['payment']) && $productsItem['payment']) {
            return $productsItem['payment'];
        }

        $costDiff = (isset($productsItem['cost_diff'])) ? $productsItem['cost_diff'] : 1;
        $xml = $productsItem;

        if (isset($productsItem['content_xml'])) {
            $xml = $productsItem['content_xml'];
        } elseif (isset($productsItem['dataXml'])) {
            $xml = $productsItem['dataXml'];
        }

        if (!is_object($xml)) {
            $xml = new SimpleXMLElement(stripslashes($xml));
        }

        $templateId = (string)$xml->template;
        $item = (!$defaultItem) ? (string)$xml->item : $defaultItem;

        if (self::$productsPrices == null) {
            $products = new Products();
            self::$productsPrices = $products->getProductsPrices();
        }

        $defaultPrice = (isset(self::$productsPrices[$item . $templateId]))
            ? self::$productsPrices[$item . $templateId]
            : 0;

        return round($defaultPrice * $costDiff) . '.00';
    }


    public function getUserList()
    {
        $select = $this->select()
            ->distinct()
            ->from(
                ['p_i' => self::$_tableName],
                ['user_id']
            )
            ->join(['u' => Users::$_tableName], 'p_i.user_id = u.id', ['user_name'])
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Fetch product by id with related data
     *
     * @param $id
     * @return null
     */
    public function fetchProduct($id)
    {
        $cacheRecordId = 'products_item_' . $id;
        if ($this->isCached($cacheRecordId)) {
            $product = $this->loadFromCache($cacheRecordId);
            $product['content_xml'] = new SimpleXMLElement(stripslashes($product['content_xml']));

            return $product;
        }

        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(
                ['p_i' => self::$_tableName],
                [
                    'id', 'user_id', 'content_xml', 'color', 'allowed_colors', 'cost_diff', 'ru', 'ua',
                    'header_ru', 'header_ua', 'description_ru', 'description_ua', 'updated'
                ]
            )
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', ['group_name' => 'name'])
            ->joinLeft(['p_i_theme' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_theme.products_items_id', ['products_themes_id'])
            ->joinLeft(['p_i_tag' => ProductsItemsTags::$_tableName], 'p_i.id = p_i_tag.products_items_id', ['products_tags_id'])
            ->where('p_i.id = ?', $id)
            ->setIntegrityCheck(false);

        $themeId = $this->getProductThemeId($id);
        if ($themeId) {
            $select->where('p_i_theme.products_themes_id = ?', $themeId);
        }

        $tagId = $this->getProductTagId($id);
        if ($tagId) {
            $select->where('p_i_tag.products_tags_id = ?', $tagId);
        }

        $products = $this->fetchAll($select)->toArray();

        if (!isset($products[0])) {
            return null;
        }

        $product = $products[0];
        $this->saveInCache($product, $cacheRecordId);

        $product['content_xml'] = new SimpleXMLElement(stripslashes($product['content_xml']));

        return $product;
    }

    public function getUpdatedProductItemXml($id, $item, $color, $size)
    {
        $productsItem = $this->fetchRow("id = $id")->toArray();

        $xmlData = new SimpleXMLElement(stripslashes($productsItem['content_xml']));
        $xmlData->item = $item;

        if ($color) {
            $xmlData->color = $color;
        }

        if ($size) {
            $xmlData->size = $size;
        }

        if ('tshirt' == (string)$xmlData->group) {
            foreach (['front', 'back'] as $side) {
                $colorImg = $xmlData->xpath("//{$xmlData->color}/img[@type=\"$side\"]");

                if (isset($colorImg[0])) {
                    $img = $xmlData->xpath("//img[@type=\"$side\"]");
                    $img[0]->url = $colorImg[0]->url;
                    $img[0]->width = $colorImg[0]->width;
                    $img[0]->height = $colorImg[0]->height;
                    $img[0]->top = $colorImg[0]->top;
                    $img[0]->left = $colorImg[0]->left;
                }
            }

            foreach (ProductsItems::$tShirtAllColors as $color) {
                if ($xmlData->{$color}) {
                    unset($xmlData->{$color});
                }
            }
        }

        $productsItem['xmlData'] = $xmlData;

        return $productsItem;
    }

    /**
     * Get products page count for catalog
     *
     * @param $group
     * @param null $themeId
     * @param null $tagId
     * @return int
     */
    public function getProductsPageCount($group, $themeId = null, $tagId = null)
    {
        $cacheRecordId = 'products_fot_group_' . $group . '_with_theme_' . ((int)$themeId) . '_and_tag_' . ((int)$tagId) . '_page_count';
        if ($this->isCached($cacheRecordId)) {
            return $this->loadFromCache($cacheRecordId);
        }

        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['count(*) as amount'])
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', [])
            ->where('p_i.status = ?', 'public')
            ->where('p_g.name = ?', $group)
            ->setIntegrityCheck(false);

        if ($themeId) {
            $select->join(['p_i_theme' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_theme.products_items_id', [])
                ->where('p_i_theme.products_themes_id = ?', $themeId);
        }

        if ($tagId) {
            $select->join(['p_i_tag' => ProductsItemsTags::$_tableName], 'p_i.id = p_i_tag.products_items_id', [])
                ->where('p_i_tag.products_tags_id = ?', $tagId);
        }

        $productsCountObj = $this->fetchAll($select);
        $pagesAmount = (int)ceil($productsCountObj[0]->amount / self::$productsOnPage);
        $this->saveInCache($pagesAmount, $cacheRecordId);

        return $pagesAmount;
    }

    /**
     * Get products for catalog
     *
     * @param $group
     * @param null $themeId
     * @param null $tagId
     * @param int $page
     * @return array
     */
    public function getProductsByPage($group, $item, $themeId = null, $tagId = null, $page = 1)
    {
        $session = new Zend_Session_Namespace('breadcrumbs');
        $session->breadcrumbs = [
            'themeId' => $themeId,
            'tagId'   => $tagId
        ];

        $cacheRecordId = 'products_fot_group_' . $group . '_with_theme_' . ((int)$themeId) . '_and_tag_' . ((int)$tagId);
        if ($this->isCached($cacheRecordId)) {
            return $this->loadFromCache($cacheRecordId);
        }

        $orderJoinParams = 'p_o.product_id = p_i.id';
        $orderJoinParams .= ($themeId) ? ' AND p_o.theme_id = ' . $themeId : ' AND p_o.theme_id IS NULL';
        $orderJoinParams .= ($tagId) ? ' AND p_o.tag_id = ' . $tagId : ' AND p_o.tag_id IS NULL';

        $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');

        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['id', 'content_xml', 'ru', 'ua', 'updated', 'color', 'cost_diff'])
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', ['group_name' => 'name'])
            ->joinLeft(
                ['s_r' => SeoRoute::$_tableName],
                "s_r.lang='$language' AND s_r.controller='produces' AND s_r.action='catalog' AND " .
                "s_r.param1_name='item' AND s_r.param1_val='$item' AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                "s_r.get1_name='id' AND s_r.get1_val=p_i.id AND s_r.get2_name='' AND s_r.get2_val=''",
                ['seo_url']
            )
            ->joinLeft(['p_o' => ProductsOrders::$_tableName], $orderJoinParams, ['order'])
            ->where('p_i.status = ?', 'public')
            ->where('p_g.name = ?', $group)
            ->limit(self::$productsOnPage, ($page - 1) * self::$productsOnPage)
            ->order(['order ASC', 'id DESC'])
            ->setIntegrityCheck(false);

        if ($themeId) {
            $select->join(['p_i_theme' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_theme.products_items_id', [])
                ->where('p_i_theme.products_themes_id = ?', $themeId);
        }

        if ($tagId) {
            $select->join(['p_i_tag' => ProductsItemsTags::$_tableName], 'p_i.id = p_i_tag.products_items_id', [])
                ->where('p_i_tag.products_tags_id = ?', $tagId);
        }

        $products = $this->fetchAll($select)->toArray();
        $this->saveInCache($products, $cacheRecordId);

        return $products;
    }

    /**
     * Get similar products for view product page (Not cashed because product should be random)
     * DO NOT CACHE
     *
     * @param $themeId
     * @param $tagId
     * @return array
     */
    public function getSimilarProducts($item, $itemId, $themeId, $tagId)
    {
        $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');

        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['id', 'content_xml', 'ru', 'ua', 'updated', 'color', 'cost_diff'])
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', ['group_name' => 'name'])
            ->where('status = ?', 'public')
            ->where('p_i.id != ?', $itemId)
            ->order('RAND()')
            ->limit(3)
            ->setIntegrityCheck(false);

        if ($themeId) {
            $select->join(['p_i_theme' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_theme.products_items_id', [])
                ->where('p_i_theme.products_themes_id = ?', $themeId);
        }

        if ($tagId) {
            $select->join(['p_i_tag' => ProductsItemsTags::$_tableName], 'p_i.id = p_i_tag.products_items_id', [])
                ->where('p_i_tag.products_tags_id = ?', $tagId);
        }

        $products = $this->fetchAll($select)->toArray();

        foreach ($products as $key => $product) {
            $select = $this->select()
                ->from(['p' => Products::$_tableName], ['id', 'name'])
                ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p.group_id', ['group_name' => 'name'])
                ->joinLeft(
                    ['s_r' => SeoRoute::$_tableName],
                    "s_r.lang='$language' AND s_r.controller='produces' AND s_r.action='catalog' AND " .
                    "s_r.param1_name='item' AND s_r.param1_val=p.name AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                    "s_r.get1_name='id' AND s_r.get1_val={$product['id']} AND s_r.get2_name='' AND s_r.get2_val=''",
                    ['seo_url']
                )
                ->where('p_g.name = ?', $product['group_name'])
                ->where('p.published = ?', 1)
                ->order('RAND()')
                ->limit(1)
                ->setIntegrityCheck(false);

            $productUrl = $this->fetchRow($select)->toArray();
            $products[$key]['item'] = $productUrl['name'];
            $products[$key]['seo_url'] = $productUrl['seo_url'];
        }

        return $products;
    }

    /**
     * Get products page count for product manage page (admin and partner)
     *
     * @param int $userId
     * @param int $groupId
     * @return float
     */
    public function getUserProductsPageCount($userId = null, $groupId = null)
    {
        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['count(*) as amount'])
            ->setIntegrityCheck(false);

        if ($userId) {
            $select->where('p_i.user_id = ?', $userId);
        }

        if ($groupId) {
            $select->where('p_i.group_id = ?', $groupId);
        }

        $productsCountObj = $this->fetchAll($select);
        $pagesAmount = ceil($productsCountObj[0]->amount / self::$productsOnPage);

        return $pagesAmount;
    }

    /**
     * Get products for product manage page (admin and partner)
     *
     * @param int $page
     * @param int $userId
     * @param int $groupId
     * @return array
     */
    public function getUserProductsByPage($page = 1, $userId = null, $groupId = null)
    {
        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['id', 'content_xml', 'ru', 'ua', 'status', 'updated', 'color', 'user_id'])
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', ['group_name' => 'name'])
            ->limit(self::$productsOnPage, ($page - 1) * self::$productsOnPage)
            ->order('p_i.id DESC')
            ->setIntegrityCheck(false);

        if ($userId) {
            $select->where('p_i.user_id = ?', $userId);
        }

        if ($groupId) {
            $select->where('p_i.group_id = ?', $groupId);
        }

        $products = $this->fetchAll($select)->toArray();

        $selectedProductIds = array_map(
            function ($productItem) {
                return $productItem['id'];
            },
            $products
        );

        $tags = [];
        $themes = [];
        if ($selectedProductIds) {
            $selectTags = $this->select()
                ->from(['p_t' => ProductsTags::$_tableName], ['ru', 'ua'])
                ->join(['p_i_t' => ProductsItemsTags::$_tableName], 'p_t.id = p_i_t.products_tags_id', ['products_items_id', 'products_tags_id'])
                ->where('p_i_t.products_items_id IN (?)', $selectedProductIds)
                ->setIntegrityCheck(false);
            $tags = $this->fetchAll($selectTags)->toArray();

            $selectThemes = $this->select()
                ->from(['p_t' => ProductsThemes::$_tableName], ['ru', 'ua'])
                ->join(['p_i_t' => ProductsItemsThemes::$_tableName], 'p_t.id = p_i_t.products_themes_id', ['products_items_id', 'products_themes_id'])
                ->where('p_i_t.products_items_id IN (?)', $selectedProductIds)
                ->setIntegrityCheck(false);
            $themes = $this->fetchAll($selectThemes)->toArray();
        }

        foreach ($products as $key => $product) {
            $products[$key]['content_xml'] = new SimpleXMLElement(stripslashes($product['content_xml']));

            $products[$key]['tags'] = array_filter(
                $tags,
                function ($tag) use ($product) {
                    if ($product['id'] == $tag['products_items_id']) {
                        return true;
                    }
                }
            );

            $products[$key]['themes'] = array_filter(
                $themes,
                function ($tag) use ($product) {
                    if ($product['id'] == $tag['products_items_id']) {
                        return true;
                    }
                }
            );
        }

        return $products;
    }

    /**
     * Get product details for admin edit page
     *
     * @param $id
     * @param null $userId
     * @return null
     */
    public function getProductDetails($id, $userId = null)
    {
        /** @var Zend_Db_Table_Select $select */
        $select = $this->select()
            ->from(
                ['p_i' => self::$_tableName],
                [
                    'id', 'user_id', 'content_xml', 'ru', 'ua', 'color', 'allowed_colors', 'cost_diff', 'status',
                    'title_ua', 'title_ru', 'header_ua', 'header_ru', 'description_ru', 'description_ua', 'text_ua', 'text_ru'
                ]
            )
            ->joinLeft(['p_i_theme' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_theme.products_items_id', ['products_themes_id'])
            ->joinLeft(['p_i_tag' => ProductsItemsTags::$_tableName], 'p_i.id = p_i_tag.products_items_id', ['products_tags_id'])
            ->where("p_i.id = '$id'")
            ->setIntegrityCheck(false);

        $products = $this->fetchAll($select)->toArray();

        if (!isset($products[0])) {
            return null;
        }

        $product = $products[0];
        $itemThemes = [];
        $itemTags = [];

        foreach ($products as $item) {
            $itemThemes[] = $item['products_themes_id'];
            $itemTags[] = $item['products_tags_id'];
        }

        $product['itemThemeIds'] = array_unique(array_filter($itemThemes));
        $product['itemTagIds'] = array_unique(array_filter($itemTags));

        return $product;
    }

    /**
     * User product for edit page
     *
     * @param $id
     * @param null $userId
     * @return null|SimpleXMLElement
     */
    public function getUserProduct($id, $userId = null)
    {
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], '*')
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', ['group_name' => 'name'])
            ->join(['p_t' => ProductsThemes::$_tableName], 'p_t.id = p_i.theme_id', ['theme_ru' => 'ru', 'theme_ua' => 'ua'])
            ->setIntegrityCheck(false);

        if ($userId == null) {
            $select->where("p_i.id = $id");
        } else {
            $select->where("p_i.user_id = $userId AND p_i.id = $id");
        }

        $products = $this->fetchAll($select)->toArray();

        return (isset($products[0])) ? new SimpleXMLElement(stripslashes($products[0]['content_xml'])) : null;
    }

    // ---------------------------------------------------------------------
    // Don't know
    // ---------------------------------------------------------------------

    public function getUserOrderedProducts($userId, $shopCode, $payOutId = null)
    {
        if (self::$productsBenefitGrouped == null) {
            $select = $this->select()
                ->from(
                    ['o' => Orders::$_tableName],
                    ['o.id', 'o.date', 'o.shop_code', 'o.status']
                )
                ->join(
                    ['b' => Baskets::$_tableName],
                    'o.id = b.order_id',
                    ['basket_item_id' => 'b.id', 'b.count', 'b.dataXml']
                )
                ->join(
                    ['p_i' => ProductsItems::$_tableName],
                    'b.product_item_id = p_i.id',
                    ['owner_id' => 'p_i.user_id']
                )
                ->setIntegrityCheck(false);
            if ($payOutId) {
                $select->where("(p_i.user_id = '$userId' OR o.shop_code='$shopCode')");
            } else {
                $select->where("(p_i.user_id = '$userId' AND b.users_pay_out_id IS NULL)
                        OR (o.shop_code='$shopCode' AND o.users_pay_out_id IS NULL)");
            }
            $myOrderedProducts = $this->fetchAll($select)->toArray();
            $products = new Products();
            $productsPrices = $products->getProductsPrices();
            $productsGrouped = [];
            $benefitDone = 0;
            $benefitInProcess = 0;
            $benefitCanceled = 0;
            foreach ($myOrderedProducts as $value) {
                if (!isset($productsGrouped[$value['id']])) {
                    $productsGrouped[$value['id']] = [
                        'id'           => $value['id'],
                        'ordered'      => date('d/m/Y', strtotime($value['date'])),
                        'totalbenefit' => 0,
                        'status'       => $value['status'],
                        'shop_code'    => $value['shop_code'],
                        'items'        => []
                    ];
                }
                $itemXml = new SimpleXMLElement(stripslashes($value['dataXml']));
                $itemName = (string)$itemXml->item;
                $price = $productsPrices[$itemName] * $value['count'];
                $itembenefit = ($shopCode == $value['shop_code']) ? $price / 10 : 0;
                $itembenefit += ($userId == $value['owner_id']) ? $price / 10 : 0;
                $item = [
                    'basket_item_id' => $value['basket_item_id'],
                    'item'           => $itemName,
                    'count'          => $value['count'],
                    'price'          => $price,
                    'byref'          => ($shopCode == $value['shop_code']) ? '10%' : '0%',
                    'byprod'         => ($userId == $value['owner_id']) ? '10%' : '0%',
                    'itembenefit'    => $itembenefit
                ];
                $productsGrouped[$value['id']]['totalbenefit'] += $itembenefit;
                $productsGrouped[$value['id']]['items'][] = $item;
                if ($value['status'] == 'closed') {
                    $benefitDone += $itembenefit;
                } elseif ($value['status'] == 'canceled') {
                    $benefitCanceled += $itembenefit;
                } else {
                    $benefitInProcess += $itembenefit;
                }
            }
            self::$productsBenefitGrouped = [
                'data'             => $productsGrouped,
                'benefitDone'      => $benefitDone,
                'benefitInProcess' => $benefitInProcess,
                'benefitCanceled'  => $benefitCanceled
            ];
        }

        return self::$productsBenefitGrouped;
    }

    public function getUserOrderedProductsBenefit($userId, $shopCode)
    {
        $productsGrouped = $this->getUserOrderedProducts($userId, $shopCode);
        $total = 0;
        foreach ($productsGrouped['data'] as $value) {
            if ($value['status'] == 'closed') {
                $total += $value['totalbenefit'];
            }
        }

        return $total;
    }

    public function getUserProductsCount($userId)
    {
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], 'count(*) AS count')
            ->where("p_i.user_id = $userId")
            ->setIntegrityCheck(false);
        $products = $this->fetchAll($select)->toArray();

        return (isset($products[0]['count'])) ? $products[0]['count'] : 0;
    }


    public function getAllItemsSeoInfo()
    {
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['id', 'ua', 'ru']);

        return $this->fetchAll($select)->toArray();
    }


    public function getAllItemsSeoInfoNew()
    {
        $select = $this->select()
            ->from(['p_i' => self::$_tableName], ['id', 'group_id', 'ua', 'ru', 'created'])
            ->join(
                ['p_g' => ProductsGroup::$_tableName],
                'p_g.id = p_i.group_id',
                ['group_id' => 'id', 'group_name' => 'name']
            )
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }

    // ----------------------------------------------------------

    /**
     * Take get current theme id for product (from session if exist)
     *
     * @param $id
     * @return mixed
     */
    private function getProductThemeId($id)
    {
        /** @var Zend_Db_Table_Select $select */
        $selectThemeId = $this->select()
            ->from(['p_i_theme' => ProductsItemsThemes::$_tableName], ['products_themes_id'])
            ->where("p_i_theme.products_items_id = '$id'")
            ->setIntegrityCheck(false);

        $selectedThemes = $this->fetchAll($selectThemeId)->toArray();

        if (count($selectedThemes) == 0) {
            return null;
        }

        $session = new Zend_Session_Namespace('breadcrumbs');

        if (!isset($session->breadcrumbs) || !$session->breadcrumbs['themeId']) {
            return $selectedThemes[0]['products_themes_id'];
        }

        $themeId = $session->breadcrumbs['themeId'];
        $selectedThemeIds = array_map(
            function ($theme) {
                return $theme['products_themes_id'];
            },
            $selectedThemes
        );

        if (in_array($themeId, $selectedThemeIds, true)) {
            return $themeId;
        }

        return $selectedThemes[0]['products_themes_id'];
    }

    private function getProductTagId($id)
    {
        /** @var Zend_Db_Table_Select $select */
        $selectEventId = $this->select()
            ->from(['p_i_tag' => ProductsItemsTags::$_tableName], ['products_tags_id'])
            ->where("p_i_tag.products_items_id = '$id'")
            ->setIntegrityCheck(false);

        $selectedTags = $this->fetchAll($selectEventId)->toArray();

        if (count($selectedTags) == 0) {
            return null;
        }

        $session = new Zend_Session_Namespace('breadcrumbs');
        if (!isset($session->breadcrumbs) || !$session->breadcrumbs['tagId']) {
            return $selectedTags[0]['products_tags_id'];
        }

        $tagId = $session->breadcrumbs['tagId'];

        $selectedTagIds = array_map(
            function ($tag) {
                return $tag['products_tags_id'];
            },
            $selectedTags
        );

        if (in_array($tagId, $selectedTagIds, true)) {
            return $tagId;
        }

        return $selectedTags[0]['products_tags_id'];
    }
}
