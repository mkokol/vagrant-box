<?php

class SeoRoute extends Model
{
    public static $_tableName = 'seo_route';

    public function fetchSEORow($requestUrlSEO)
    {
        $requestUrlSEOObj = explode('?', $requestUrlSEO);
        $urlSeo = $requestUrlSEOObj[0];

        $select = $this->select()
            ->from(['s_r' => self::$_tableName], '*')
            ->where('s_r.seo_url = ?', $urlSeo)
            ->orWhere('s_r.url = ?', $requestUrlSEO)
            ->order('s_r.type ASC')
            ->setIntegrityCheck(false);

        $seoUrl = $this->fetchAll($select)->toArray();

        return (isset($seoUrl[0])) ? $seoUrl[0] : null;
    }

    public function fetchLanguageSEORows($lang, $requestUrlSEO)
    {
        $requetUrlSEOArray = $this->fetchSEORow($requestUrlSEO);
        if (!$requetUrlSEOArray && strpos($requestUrlSEO, $lang)) {
            $requestUrlSEO = '/' . (($lang == 'ru') ? 'ua' : 'ru')
                . '/' . ((false !== strpos($requestUrlSEO, "/$lang/")) ? explode("/$lang/", $requestUrlSEO)[1] : 0);
            $requetUrlSEOArray = $this->fetchSEORow($requestUrlSEO);
        }

        if (!$requetUrlSEOArray) {
            return [];
        }

        $select = $this->select()
            ->from(array('s_r' => self::$_tableName), '*')
            ->where(
                's_r.controller = \'' . $requetUrlSEOArray['controller'] . '\' AND s_r.action = \'' . $requetUrlSEOArray['action'] . '\' AND ' .
                's_r.param1_name = \'' . $requetUrlSEOArray['param1_name'] . '\' AND s_r.param1_val = \'' . $requetUrlSEOArray['param1_val'] . '\' AND ' .
                's_r.param2_name = \'' . $requetUrlSEOArray['param2_name'] . '\' AND s_r.param2_val = \'' . $requetUrlSEOArray['param2_val'] . '\' AND ' .
                's_r.get1_name = \'' . $requetUrlSEOArray['get1_name'] . '\' AND s_r.get1_val = \'' . $requetUrlSEOArray['get1_val'] . '\' AND ' .
                's_r.get2_name = \'' . $requetUrlSEOArray['get2_name'] . '\' AND s_r.get2_val = \'' . $requetUrlSEOArray['get2_val'] . '\' AND ' .
                's_r.type = \'route\''
            )
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    public function fetchSEORecord($requetUrlSEOArray)
    {
        $select = $this->select()
            ->from(array('s_r' => self::$_tableName), '*')
            ->where(
                's_r.lang = \'' . $requetUrlSEOArray['lang'] . '\' AND ' .
                's_r.controller = \'' . $requetUrlSEOArray['controller'] . '\' AND s_r.action = \'' . $requetUrlSEOArray['action'] . '\' AND ' .
                's_r.param1_name = \'' . $requetUrlSEOArray['param1_name'] . '\' AND s_r.param1_val = \'' . $requetUrlSEOArray['param1_val'] . '\' AND ' .
                's_r.param2_name = \'' . $requetUrlSEOArray['param2_name'] . '\' AND s_r.param2_val = \'' . $requetUrlSEOArray['param2_val'] . '\' AND ' .
                's_r.get1_name = \'' . $requetUrlSEOArray['get1_name'] . '\' AND s_r.get1_val = \'' . $requetUrlSEOArray['get1_val'] . '\' AND ' .
                's_r.get2_name = \'' . $requetUrlSEOArray['get2_name'] . '\' AND s_r.get2_val = \'' . $requetUrlSEOArray['get2_val'] . '\' AND ' .
                's_r.type = \'route\''
            )
            ->setIntegrityCheck(false);
        $record = $this->fetchAll($select)->toArray();
        return isset($record[0]) ? $record[0] : null;
    }

    /**********************************************************************************************
     * functions for XML site map
     **********************************************************************************************/

    /**
     * Collect data for building themes urls
     *
     * @return array
     */
    public function getSiteMapXmlThemes()
    {
        $selectThemesAll = $this->select()
            ->distinct()
            ->from(
                ['p_i' => ProductsItems::$_tableName],
                []
            )
            ->join(
                ['p' => Products::$_tableName],
                'p.group_id = p_i.group_id',
                ['group_id' => 'id', 'product_item' => 'name']
            )
            ->join(
                ['p_g' => ProductsGroup::$_tableName],
                'p_g.id = p.group_id',
                [new Zend_Db_Expr('NULL as theme_id')]
            )
            ->joinLeft(
                ['s_r' => SeoRoute::$_tableName],
                "s_r.controller='produces' AND s_r.action='catalog' AND " .
                "s_r.param1_name='item' AND s_r.param1_val=p.name AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                "s_r.get1_name='' AND s_r.get1_val='' AND s_r.get2_name='' AND s_r.get2_val=''",
                ['seo_url']
            )
            ->where("p.published = ?", 1)
            ->where('p_i.status = ?', 'public')
            ->setIntegrityCheck(false);

        $selectThemesIds = $this->select()
            ->distinct()
            ->from(
                ['p_i' => ProductsItems::$_tableName],
                []
            )
            ->join(
                ['p' => Products::$_tableName],
                'p.group_id = p_i.group_id',
                ['group_id' => 'id', 'product_item' => 'name']
            )
            ->join(
                ['p_i_t' => ProductsItemsThemes::$_tableName],
                'p_i.id = p_i_t.products_items_id',
                []
            )
            ->join(
                ['p_t' => ProductsThemes::$_tableName], 'p_t.id = p_i_t.products_themes_id', ['theme_id' => 'id'])
            ->joinLeft(
                ['s_r' => SeoRoute::$_tableName],
                "s_r.controller='produces' AND s_r.action='catalog' AND " .
                "s_r.param1_name='item' AND s_r.param1_val=p.name AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                "s_r.get1_name='themeId' AND s_r.get1_val=p_t.id AND s_r.get2_name='' AND s_r.get2_val=''",
                ['seo_url']
            )
            ->where("p.published = ?", 1)
            ->where('p_i.status = ?', 'public')
            ->setIntegrityCheck(false);

        $select = $this->select()
            ->distinct()
            ->union(array($selectThemesAll, $selectThemesIds))
            ->order('group_id ASC')
            ->order('theme_id ASC');

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Collect data for building sub themes urls
     *
     * @return array
     */
    public function getSiteMapXmlSubThemes()
    {

        $select = $this->select()
            ->distinct()
            ->from(
                ['p_i' => ProductsItems::$_tableName],
                []
            )
            ->join(
                ['p' => Products::$_tableName],
                'p.group_id = p_i.group_id',
                ['product_item' => 'name']
            )
            ->join(
                ['p_g' => ProductsGroup::$_tableName],
                'p_g.id = p.group_id',
                ['ordered']
            )
            ->join(
                ['p_i_themes' => ProductsItemsThemes::$_tableName],
                'p_i.id = p_i_themes.products_items_id',
                []
            )
            ->join(
                ['p_themes' => ProductsThemes::$_tableName],
                'p_themes.id = p_i_themes.products_themes_id',
                ['theme_id' => 'id']
            )
            ->join(
                ['p_i_tags' => ProductsItemsTags::$_tableName],
                'p_i.id = p_i_tags.products_items_id',
                []
            )
            ->join(
                ['p_tags' => ProductsTags::$_tableName],
                'p_tags.id = p_i_tags.products_tags_id',
                ['tag_id' => 'id']
            )
            ->joinLeft(
                ['s_r1' => SeoRoute::$_tableName],
                "s_r1.controller='produces' AND s_r1.action='catalog' AND " .
                "s_r1.param1_name='item' AND s_r1.param1_val=p.name AND s_r1.param2_name='' AND s_r1.param2_val='' AND " .
                "s_r1.get1_name='themeId' AND s_r1.get1_val=p_themes.id AND s_r1.get2_name='tagId' AND s_r1.get2_val=p_tags.id",
                ['tag_seo_url' => 'seo_url']
            )
            ->joinLeft(
                ['s_r2' => SeoRoute::$_tableName],
                "s_r2.controller='produces' AND s_r2.action='catalog' AND " .
                "s_r2.param1_name='item' AND s_r2.param1_val=p.name AND s_r2.param2_name='' AND s_r2.param2_val='' AND " .
                "s_r2.get1_name='themeId' AND s_r2.get1_val=p_themes.id AND s_r2.get2_name='' AND s_r2.get2_val='' AND " .
                "s_r1.seo_url IS NULL",
                ['theme_seo_url' => 'seo_url']
            )
            ->where("p.published = ?", 1)
            ->where('p_i.status = ?', 'public')
            ->order('p_g.ordered ASC')
            ->order('p.id ASC')
            ->order('theme_id ASC')
            ->order('tag_id ASC')
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Collect data for building products urls
     *
     * @return array
     */
    public function getSiteMapXmlItems()
    {
        $select = $this->select()
            ->distinct()
            ->from(
                ['p_i' => ProductsItems::$_tableName],
                ['item_id' => 'id']
            )
            ->join(
                ['p' => Products::$_tableName],
                'p.group_id = p_i.group_id',
                ['product_id' => 'id', 'product_item' => 'name']
            )
            ->join(
                ['p_g' => ProductsGroup::$_tableName],
                'p_g.id = p.group_id',
                ['group_id' => 'id']
            )
            ->joinLeft(
                ['p_i_themes' => ProductsItemsThemes::$_tableName],
                'p_i.id = p_i_themes.products_items_id',
                []
            )
            ->joinLeft(
                ['p_themes' => ProductsThemes::$_tableName],
                'p_themes.id = p_i_themes.products_themes_id',
                ['theme_id' => 'id']
            )
            ->joinLeft(
                ['p_i_tags' => ProductsItemsTags::$_tableName],
                'p_i.id = p_i_tags.products_items_id',
                []
            )
            ->joinLeft(
                ['p_tags' => ProductsTags::$_tableName],
                'p_tags.id = p_i_tags.products_tags_id',
                ['tag_id' => 'id']
            )
            ->joinLeft(
                ['s_r' => SeoRoute::$_tableName],
                "s_r.controller='produces' AND s_r.action='catalog' AND " .
                "s_r.param1_name='item' AND s_r.param1_val=p.name AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                "s_r.get1_name='id' AND s_r.get1_val=p_i.id AND s_r.get2_name='' AND s_r.get2_val=''",
                ['item_seo_url' => 'seo_url']
            )
            ->where("p_i.status = ?", 'public')
            ->where("p.published = ?", 1)
            ->order('p_g.id ASC')
            ->order('theme_id ASC')
            ->order('tag_id ASC')
            ->order('item_id ASC')
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }
}
