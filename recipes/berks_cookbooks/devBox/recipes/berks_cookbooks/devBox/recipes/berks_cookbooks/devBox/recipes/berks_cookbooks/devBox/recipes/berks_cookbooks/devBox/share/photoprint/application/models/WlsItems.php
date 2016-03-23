<?php

class WlsItems extends Model
{

    public static $_tableName = 'wls_items';
    public static $productsOnPage = 20;

    public static $tShirtColors = array('white', 'black');
    public static $tShirtAllColors = array('white', 'black');

    /**
     * Get white label product count
     *
     * @param $wlId
     * @param $group
     * @return int
     */
    public function getWlProductsPageCount($wlId, $group = null)
    {
        $where = "wl_i.status != 'deleted' AND wl_i.wl_id = '$wlId'";
        $where .= ($group) ? " AND wl_i.group_id = '$group'" : '';
        $select = $this->select()
            ->from(array('wl_i' => self::$_tableName), array('count(*) as amount'))
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p_g.id = wl_i.group_id', array('group_name' => 'name'))
            ->join(array('p_t' => ProductsThemes::$_tableName), 'p_t.id = wl_i.theme_id', array('theme_ru' => 'ru', 'theme_ua' => 'ua'))
            ->where($where)
            ->setIntegrityCheck(false);
        $productsCountObj = $this->fetchAll($select);
        return ceil($productsCountObj[0]->amount / self::$productsOnPage);
    }

    /**
     * Get white label products by page
     *
     * @param int $wlId
     * @param $group
     * @param int $page
     * @return array
     */
    public function getWlProductsByPage($wlId, $group = null, $page = 1)
    {
        $where = "wl_i.status != 'deleted' AND wl_i.wl_id = '$wlId'";
        $where .= ($group) ? " AND wl_i.group_id = '$group'" : '';
        $select = $this->select()
            ->from(array('wl_i' => self::$_tableName), '*')
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p_g.id = wl_i.group_id', array('group_name' => 'name'))
            ->joinLeft(array('p_t' => ProductsThemes::$_tableName), 'p_t.id = wl_i.theme_id', array('theme_ru' => 'ru', 'theme_ua' => 'ua'))
            ->where($where)
            ->limit(self::$productsOnPage, ($page - 1) * self::$productsOnPage)
            ->order('wl_i.id DESC')
            ->setIntegrityCheck(false);
        $products = $this->fetchAll($select)->toArray();
        foreach ($products as $key => $product) {
            $products[$key]['content_xml'] = new SimpleXMLElement(stripslashes($product['content_xml']));
        }
        return $products;
    }

    /**
     * Load white label product instance XML as object
     *
     * @param $id
     * @param $wlId
     * @return null|SimpleXMLElement
     */
    public function getWlProduct($id, $wlId)
    {
        $select = $this->select()
            ->from(array('wl_i' => self::$_tableName), '*')
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p_g.id = wl_i.group_id', array('group_name' => 'name'))
            ->join(array('wl_t' => WlsThemes::$_tableName), 'wl_t.id = wl_i.theme_id', array('theme' => 'name', 'theme_ru' => 'ru', 'theme_ua' => 'ua'))
            ->where("wl_i.wl_id = '{$wlId}' AND wl_i.id = '{$id}'")
            ->setIntegrityCheck(false);
        $products = $this->fetchAll($select)->toArray();
        return (isset($products[0])) ? new SimpleXMLElement(stripslashes($products[0]['content_xml'])) : null;
    }

    /**
     * Get all white label products for WL API
     *
     * @param $wlId
     * @return array
     */
    public function getAllWlProducts($wlId)
    {
        $select = $this->select()
            ->from(array('wl_i' => self::$_tableName), array('id', 'group_id', 'theme_id', 'template_id', 'content_xml', 'status'))
            ->join(array('p_g' => ProductsGroup::$_tableName), 'p_g.id = wl_i.group_id', array('group_name' => 'name'))
            ->joinLeft(array('p_t' => ProductsThemes::$_tableName), 'p_t.id = wl_i.theme_id', array())
            ->where("wl_i.wl_id = '$wlId'")
            ->order('wl_i.id DESC')
            ->setIntegrityCheck(false);
        $products = $this->fetchAll($select)->toArray();
        return $products;
    }
}