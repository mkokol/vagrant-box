<?php

class ProductsItemsThemes extends Model
{
    public static $_tableName = 'products_items_themes';

    public function saveProductThemes($itemId, $themeIds)
    {
        if(!$themeIds){
            return;
        }

        if (!is_array($themeIds)) {
            $themeIds = explode(',', $themeIds);
        }

        $select = $this->select()
            ->from(array('p_i_t' => self::$_tableName), '*')
            ->where('p_i_t.products_items_id = ?', $itemId)
            ->setIntegrityCheck(false);

        $productItemThemes = $this->fetchAll($select)->toArray();
        foreach ($productItemThemes as $itemTheme) {
                if (!in_array($itemTheme['products_themes_id'], $themeIds)) {
                $this->delete([
                    'products_themes_id = ?' => $itemTheme['products_themes_id'],
                    'products_items_id = ?'  => $itemTheme['products_items_id'],
                ]);
            } else {
                $key = array_search($itemTheme['products_themes_id'], $themeIds);
                unset($themeIds[$key]);
            }
        }

        foreach ($themeIds as $themeId) {
            $this->insert([
                'products_items_id'  => $itemId,
                'products_themes_id' => $themeId
            ]);
        }

        return;
    }
}
