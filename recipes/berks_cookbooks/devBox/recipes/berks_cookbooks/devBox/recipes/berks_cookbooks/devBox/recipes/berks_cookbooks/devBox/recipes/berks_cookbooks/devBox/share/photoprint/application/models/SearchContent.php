<?php

class SearchContent extends Model
{
    public static $_tableName = 'search_content';

    public function findCompatibles($title)
    {
        $select = $this->select()
            ->from(['s_c' => self::$_tableName])
            ->join(
                ['p_i' => ProductsItems::$_tableName],
                's_c.product_item_id = p_i.id',
                [
                    'id', 'user_id', 'content_xml', 'color', 'cost_diff', 'ru', 'ua',
                    'header_ru', 'header_ua', 'description_ru', 'description_ua', 'updated'
                ]
            )
            ->join(['p_g' => ProductsGroup::$_tableName], 'p_g.id = p_i.group_id', ['group_name' => 'name'])
            ->where('MATCH (s_c.title) AGAINST (?)', $title)
            ->group('s_c.product_item_id')
            ->order('p_i.id DESC')
            ->limit(20)
            ->setIntegrityCheck(false);

        $products = $this->fetchAll($select)->toArray();

        return $products;
    }

    public function findForAutoComplete() {
//        SELECT  *, MATCH (name, screen_name) AGAINST ('+Guy +K*' IN BOOLEAN MODE) AS SCORE
//        FROM users
//        WHERE MATCH (name, screen_name) AGAINST ('+Guy +K*' IN BOOLEAN MODE)
//        ORDER BY SCORE, grade DESC
//        LIMIT 5
        return [];
    }

    public function reindex()
    {
        $this->getAdapter()->query('TRUNCATE TABLE ' . self::$_tableName);

        $productsItems = new ProductsItems();
        $products = $productsItems->getAllItemsSeoInfo();

        foreach ($products as $product) {
            $this->insert([
                'product_item_id' => $product['id'],
                'language'        => 'ua',
                'title'           => $product['ua']
            ]);
            $this->insert([
                'product_item_id' => $product['id'],
                'language'        => 'ru',
                'title'           => $product['ru']
            ]);
        }
    }
}
