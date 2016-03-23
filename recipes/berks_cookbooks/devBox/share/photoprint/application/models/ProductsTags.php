<?php

class ProductsTags extends Model
{
    use Helpers_Trait_Memcached;

    public static $_tableName = 'products_tags';

    public function getThemesTags($item, $themeId = 0)
    {
        if (!$themeId) {
            return [];
        }

        $cacheRecordId = 'tags_for_item_' . $item . '_and_theme_' . $themeId;
        if ($this->isCached($cacheRecordId)) {
            return $this->loadFromCache($cacheRecordId);
        }

        $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');

        $select = $this->select()
            ->distinct()
            ->from(['p_i' => ProductsItems::$_tableName], [])
            ->join(['p' => Products::$_tableName], 'p.group_id = p_i.group_id', [])
            ->join(['p_i_tag' => ProductsItemsTags::$_tableName], 'p_i.id = p_i_tag.products_items_id', [])
            ->join(['p_i_theme' => ProductsItemsThemes::$_tableName], 'p_i.id = p_i_theme.products_items_id', [])
            ->join(['p_t' => self::$_tableName], 'p_t.id = p_i_tag.products_tags_id', ['id', 'ru', 'ua'])
            ->joinLeft(
                array('s_r' => SeoRoute::$_tableName),
                "s_r.lang='$language' AND s_r.controller='produces' AND s_r.action='catalog' AND " .
                "s_r.param1_name='item' AND s_r.param1_val='$item' AND s_r.param2_name='' AND s_r.param2_val='' AND " .
                "s_r.get1_name='themeId' AND s_r.get1_val='$themeId' AND s_r.get2_name='' AND s_r.get2_val=''",
                array('theme_seo_url' => 'seo_url')
            )
            ->joinLeft(
                array('s_r2' => SeoRoute::$_tableName),
                "s_r2.lang='$language' AND s_r2.controller='produces' AND s_r2.action='catalog' AND " .
                "s_r2.param1_name='item' AND s_r2.param1_val='$item' AND s_r2.param2_name='' AND s_r2.param2_val='' AND " .
                "s_r2.get1_name='themeId' AND s_r2.get1_val='$themeId' AND s_r2.get2_name='tagId' AND s_r2.get2_val=p_t.id",
                array('seo_url' => 'seo_url')
            )
            ->where("p.name = '$item' AND p_i.status = 'public' AND p_i_theme.products_themes_id = '$themeId'")
            ->setIntegrityCheck(false);

        $tags = $this->fetchAll($select)->toArray();
        $this->saveInCache($tags, $cacheRecordId);

        return $tags;
    }

    /**
     * Select all themes for select theme for product
     *
     * @return array
     */
    public function getAllTags()
    {
        $select = $this->select()
            ->from(array('p_t' => self::$_tableName), ['id', 'status', 'name', 'ru', 'ua'])
            ->order('p_t.id DESC')
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }
}
