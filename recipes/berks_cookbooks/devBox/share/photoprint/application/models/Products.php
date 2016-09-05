<?php

class Products extends Model
{
    public static $_tableName = 'products';

    /**
     * Get all public products with detayls by group
     *
     * @param $group
     * @return array
     */
    public function getProductsDetails($group)
    {
        $language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
        $select = $this->select()
            ->from(array('p' => self::$_tableName), '*')
            ->joinLeft(
                array('p_g' => ProductsGroup::$_tableName),
                'p_g.id = p.group_id',
                array('products_categories_name' => 'name')
            )
            ->joinLeft(
                array('s_r' => SeoRoute::$_tableName),
                "s_r.lang='$language' AND s_r.controller='produces' AND s_r.action='catalog' AND s_r.param1_name='item' AND s_r.param1_val=p.name AND s_r.get1_name='' AND s_r.get1_val=''",
                array('seo_url')
            )
            ->where("p.published = 1 AND p_g.name = '$group'")
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Get all products with prices
     *
     * @return array
     */
    public function getAllProductsPrice()
    {
        $selectProducts = $this->select()
            ->from(array('p' => self::$_tableName), array())
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p_g.id = p.group_id', array())
            ->columns(array(
                'p.id',
                'p.group_id',
                new Zend_Db_Expr('NULL AS template_id'),
                'p.name',
                'group_name' => 'p_g.name',
                'p.price',
                'p.published',
            ))
            ->setIntegrityCheck(false);
        $selectTemplates = $this->select()
            ->from(array('p' => self::$_tableName), array())
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p_g.id = p.group_id', array())
            ->join(array('p_t' => ProductsTemplates::$_tableName), 'p_t.group_id = p.group_id', array())
            ->columns(array(
                'p.id',
                'p.group_id',
                'template_id' => 'p_t.id',
                'p.name',
                'group_name'  => 'p_g.name',
                'p_t.price',
                'p.published'
            ))
            ->setIntegrityCheck(false);
        $select = $this->select()
            ->union(array($selectProducts, $selectTemplates))
            ->order('group_id ASC')
            ->order('id ASC')
            ->order('template_id ASC');

        return $this->fetchAll($select)->toArray();
    }

    public function fetchGroupByProductName($name)
    {
        $select = $this->select()
            ->from(array('p' => self::$_tableName))
            ->join(array('p_c' => ProductsGroup::$_tableName), 'p_c.id = p.group_id', array('name'))
            ->where('p.name = \'' . $name . '\'')
            ->setIntegrityCheck(false);
        $result = $this->fetchAll($select)->toArray();

        return (isset($result[0]['name'])) ? $result[0]['name'] : '';
    }

    /**
     * Get prices for products
     *
     * @return array
     */
    public function getProductsPrices()
    {
        $select = $this->select()
            ->from(array('p' => self::$_tableName), '*')
            ->joinLeft(
                array('p_d' => ProductsTemplates::$_tableName),
                'p_d.group_id = p.group_id',
                array(
                    'products_templates_id'      => 'id',
                    'products_templates_name'    => 'name',
                    'products_templates_price'   => 'price',
                    'products_templates_default' => 'default'
                )
            )
            ->where("p.published = 1")
            // TODO: fix naming fields (order)
            ->order('order ASC')
            ->setIntegrityCheck(false);
        $productList = $this->fetchAll($select)->toArray();

        $productsPrices = array();
        if (is_array($productList)) {
            foreach ($productList as $value) {
                if (!isset($productsPrices[$value['name']])) {
                    $productsPrices[$value['name']] = $value['price'];
                }
                if ($value['products_templates_id']) {
                    if ($value['products_templates_price']) {
                        $productsPrices[$value['name'] . $value['products_templates_id']] = $value['products_templates_price'];
                    } else {
                        $productsPrices[$value['name'] . $value['products_templates_id']] = $value['price'];
                    }
                }
            }
        }

        return $productsPrices;
    }

    public function getProductsGroup()
    {
        $select = $this->select()
            ->from(['p' => self::$_tableName], '*')
            ->joinLeft(
                ['p_g' => ProductsGroup::$_tableName],
                'p_g.id = p.group_id',
                ['group_name' => 'name']
            )
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Get product rices for WL
     *
     * @param $t
     * @return array
     */
    public function getProductsPricesForWL($t)
    {
        $select = $this->select()
            ->from(array('p' => self::$_tableName), '*')
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p.group_id = p_g.id', array())
            ->joinLeft(
                array('p_d' => ProductsTemplates::$_tableName),
                'p_d.group_id = p.group_id',
                array(
                    'products_templates_id'      => 'id',
                    'products_templates_name'    => 'name',
                    'products_templates_price'   => 'price',
                    'products_templates_default' => 'default'
                )
            )
            ->where("p.published = 1 AND p_g.supported_wl = 1")
            // TODO: fix naming fields (order)
            ->order('order ASC')
            ->setIntegrityCheck(false);
        $productList = $this->fetchAll($select)->toArray();

        $productsPrices = array();
        $productsItems = array();
        if (is_array($productList)) {
            foreach ($productList as $value) {
                if (!isset($productsPrices[$value['name']])) {
                    $productsPrices[$value['name']] = $value['price'];
                }
                $price = $productsPrices[$value['name']];
                if (isset($value['products_templates_price']) && $value['products_templates_price'] != '') {
                    $price = $value['products_templates_price'];
                }
                $productsItems[] = array(
                    'group_id'    => $value['group_id'],
                    'template_id' => ($value['products_templates_id']) ? $value['products_templates_id'] : null,
                    'code'        => $value['name'],
                    'name'        => $t->_($value['name']),
                    'price'       => $price
                );
            }
        }

        return $productsItems;
    }
}
