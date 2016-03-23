<?php

class ProductsItemsTags extends Model
{
    public static $_tableName = 'products_items_tags';

    public function saveProductTags($itemId, $tagIds)
    {
        if(!$tagIds){
            return;
        }

        if (!is_array($tagIds)) {
            $tagIds = explode(',', $tagIds);
        }

        $select = $this->select()
            ->from(array('p_i_t' => self::$_tableName), '*')
            ->where('p_i_t.products_items_id = ?', $itemId)
            ->setIntegrityCheck(false);

        $produtItemTags = $this->fetchAll($select)->toArray();
        foreach ($produtItemTags as $itemTag) {
            if (!in_array($itemTag['products_tags_id'], $tagIds)) {
                $this->delete([
                    'products_tags_id = ?' => $itemTag['products_tags_id'],
                    'products_items_id = ?'  => $itemTag['products_items_id'],
                ]);
            } else {
                $key = array_search($itemTag['products_tags_id'], $tagIds);
                unset($tagIds[$key]);
            }
        }

        foreach ($tagIds as $tagId) {
            $this->insert([
                'products_items_id'  => $itemId,
                'products_tags_id' => $tagId
            ]);
        }

        return;
    }
}
