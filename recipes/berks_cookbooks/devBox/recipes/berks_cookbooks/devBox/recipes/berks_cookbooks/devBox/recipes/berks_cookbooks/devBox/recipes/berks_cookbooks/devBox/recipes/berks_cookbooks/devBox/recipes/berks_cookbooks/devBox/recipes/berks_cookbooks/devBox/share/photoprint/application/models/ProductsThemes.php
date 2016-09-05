<?php

class ProductsThemes extends Model
{

    public static $_tableName = 'products_themes';

    /**
     * Select all themes by product if they contain at list one
     *
     * @param $item
     * @return array
     */
    public function getItemsThemes($item)
    {
        $cacheRecordId = 'themes_for_' . $item;
        if($this->isCached($cacheRecordId)){
            return $this->loadFromCache($cacheRecordId);
        }

        $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');

        $select = $this->select()
            ->distinct()
            ->from(['p_i' => ProductsItems::$_tableName], [])
            ->join(['p' => Products::$_tableName], 'p.group_id = p_i.group_id', [])
            ->join(['p_i_t' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_t.products_items_id', [])
            ->join(['p_t' => self::$_tableName], 'p_t.id = p_i_t.products_themes_id', ['id', 'ru', 'ua', 'ordered'])
            ->joinLeft(
                ['s_r' => SeoRoute::$_tableName],
                "s_r.lang='$language' AND s_r.controller='produces' AND s_r.action='catalog' AND " .
                "s_r.param1_name='item' AND s_r.param1_val='$item' AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                "s_r.get1_name='themeId' AND s_r.get1_val=p_t.id AND s_r.get2_name='' AND s_r.get2_val=''",
                ['seo_url']
            )
            ->where("p.name = '$item' AND p_i.status = 'public'")
            ->order('p_t.ordered DESC')
            ->order('p_t.id ASC')
            ->setIntegrityCheck(false);

        $themes = $this->fetchAll($select)->toArray();
        $this->saveInCache($themes, $cacheRecordId);

        return $themes;
    }

    /**
     * Select all themes with sub-theme for admin view
     *
     * @return array
     */
    public function getAllThemesWithSubThemes()
    {
        $select = $this->select()
            ->distinct()
            ->from(array('p_t' => self::$_tableName), '*')
            ->order('p_t.id DESC')
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    /**
     * Select all themes for select theme for product
     *
     * @return array
     */
    public function getAllThemes()
    {
        $select = $this->select()
            ->distinct()
            ->from(array('p_t' => self::$_tableName), '*')
            ->order('p_t.id DESC')
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }
}
