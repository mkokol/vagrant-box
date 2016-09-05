<?php

class Baskets extends Model
{
    public static $_tableName = 'baskets';

    public static function getMyItemCounts()
    {
        $baskets = new Baskets();
        $select = $baskets->select()
            ->from(array('b' => self::$_tableName), array('sum(count) as amount'))
            ->where('user_id = ?', Users::getCarrentUserId())
            ->where('status = ?', 'created');

        $itemCountObj = $baskets->fetchAll($select);

        return (int) $itemCountObj[0]->amount;
    }

    public function getBasketItem($userId = null, $status = null)
    {
        $select = $this->select()
            ->from(
                ['b' => self::$_tableName],
                ['id', 'product_group', 'order_id', 'dataXml', 'item_payment' =>'payment', 'status', 'count', 'date']
            );

        if ($userId != null) {
            $select->where('user_id = ?', $userId)
                ->where('status = ?', 'created')
                ->order('date DESC');
        } else {
            $select->join(
                ['o' => Orders::$_tableName],
                'o.id = b.order_id',
                [
                    'user_name', 'phone', 'address', 'order_status' => 'status', 'payment_sys',
                    'order_payment' => 'payment', 'dostavka_lustivok', 'dostavka_else'
                ]
            )->join(
                ['u' => Users::$_tableName],
                'u.id = o.user_id',
                ['email']
            );

            if ($status == 'new') {
                $select->where('b.status = ?', 'inOrder')
                    ->orWhere('b.status = ?', 'produced')
                    ->orWhere('b.status = ?', 'sended');
            } else if ($status == 'old') {
                $selectOrders = $this->select()
//                    ->distinct()
//                    ->from(['b' => self::$_tableName], ['order_id'])
//                    ->where('b.order_id != ?', '0')
//                    ->where('b.status != ?', 'inOrder')
//                    ->where('b.status != ?', 'produced')
//                    ->where('b.status != ?', 'sended')
                    ->order('order_id DESC')
                    ->limit(500000);

                $orderIdList = $this->fetchAll($selectOrders)->toArray();
                $orderIds = array_map(
                    function ($productItem) {
                        return $productItem['order_id'];
                    },
                    $orderIdList
                );

                $select->where('order_id IN (?)', $orderIds);
            }

            $select->order('order_id DESC')
                ->setIntegrityCheck(false);
        }

        return $this->fetchAll($select)->toArray();
    }

    public function validateItem($itemXml)
    {
        $group = (string) $itemXml->group;
        $isValid = true;
        switch ($group) {
            case 'tshirt':
                $size = (string) $itemXml->size;
                $color = (string) $itemXml->color;
                $isValid = ($size != '' && $color != '') ? true : false;
                break;
        }
        return $isValid;
    }

    public function getOrdersItems($ordersId)
    {
        if (!is_array($ordersId)) {
            $ordersId = [$ordersId];
        }

        $select = $this->select()
            ->from(['b' => self::$_tableName], '*')
            ->where('b.order_id IN (' . implode(', ', $ordersId) . ')')
            ->setIntegrityCheck(false);

        return $this->fetchAll($select)->toArray();
    }
}
