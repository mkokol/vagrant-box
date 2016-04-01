<?php

class Orders extends Model
{

    public static $_tableName = 'orders';
    private $ordersOnPage = 10;

    public function getPartnerOrdersCount($code)
    {
        return ceil($this->fetchAll(' partner_code = \'' . $code . '\'', 'id DESC')->count() / $this->ordersOnPage);
    }

    public function getPartnerOrders($code, $page)
    {
        return $this->fetchAll(' partner_code = \'' . $code . '\'', 'id DESC', $this->ordersOnPage, $this->ordersOnPage * $page)->toArray();
    }

    public function getOrders($orderIdList)
    {
        $select = $this->select()
            ->from(array('o' => self::$_tableName), '*')
            ->where('o.id = ' . implode(' OR o.id = ', $orderIdList))
            ->setIntegrityCheck(false);
        $orders = $this->fetchAll($select)->toArray();
        $ordersSorted = array();

        foreach ($orders as $value) {
            $ordersSorted[$value['id']] = $value;
        }

        return $ordersSorted;
    }

    public function getUsersLastOrder($userId)
    {
        $select = $this->select()
            ->from(array('o' => self::$_tableName), '*')
            ->where('o.user_id = ' . $userId)
            ->order('id DESC')
            ->limit(1)
            ->setIntegrityCheck(false);

        return $this->fetchRow($select);
    }

    /**
     * @param $wlId
     * @return float
     */
    public function getWLOrdersPageCount($wlId)
    {
        $select = $this->select()
            ->from(array('o' => self::$_tableName), 'count(*) as count')
            ->where('o.shop_code = \'wl-' . $wlId . '\'')
            ->setIntegrityCheck(false);
        $itemCountObj = $this->fetchAll($select);
        return ceil($itemCountObj[0]->count / $this->ordersOnPage);
    }

    /**
     * Get WL orders per page
     *
     * @param $wlId
     * @param $page
     * @return array
     */
    public function getWLOrdersByPage($wlId, $page)
    {
        $select = $this->select()
            ->from(array('o' => self::$_tableName), '*')
            ->where('o.shop_code = \'wl-' . $wlId . '\'')
            ->limit($this->ordersOnPage, --$page * $this->ordersOnPage)
            ->setIntegrityCheck(false);
        $orders = $this->fetchAll($select)->toArray();
        $ordersSorted = array();
        foreach ($orders as $value) {
            $ordersSorted[$value['id']] = $value;
        }
        return $ordersSorted;
    }

}
