<?php

class ProductsOrders extends Model
{
    public static $_tableName = 'products_orders';

    public function fetchProductOrders($productIds, $themeId = null, $tagId = null)
    {
        $select = $this->select()
            ->from(array('p_o' => self::$_tableName), '*')
            ->where('p_o.product_id IN(?)', $productIds)
            ->setIntegrityCheck(false);

        if ($themeId) {
            $select->where('p_o.theme_id = ?', $themeId);
        } else {
            $select->where('p_o.theme_id IS NULL');
        }

        if ($tagId) {
            $select->where('p_o.tag_id = ?', $tagId);
        } else {
            $select->where('p_o.tag_id IS NULL');
        }

        $productOrders = $this->fetchAll($select)->toArray();
        $productOrdersSorted = [];

        foreach ($productOrders as $productOrder) {
            $productOrdersSorted[$productOrder['product_id']] = $productOrder;
        }

        return $productOrdersSorted;
    }
}

