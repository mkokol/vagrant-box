<?php

class UsersPayOut extends Model
{

    public static $_tableName = 'users_pay_out';

    /**
     * Minimum amount of money that could be payed out
     * @var int
     */
    public static $minSum = 50;

    /**
     * Get all new pey out that should be payed
     *
     * @return mixed
     */
    public function getNewPayOuts()
    {
        $select = $this->select()
            ->from(
                array('u_p_o' => self::$_tableName),
                array('u_p_o.id', 'u_p_o.created', 'u_p_o.sum', 'u_p_o.card_number', 'u_p_o.card_owner', 'u_p_o.status')
            )
            ->join(
                array('u' => Users::$_tableName),
                'u_p_o.user_id = u.id',
                array('u.user_name', 'u.email', 'u.phone')
            )
            ->where("u_p_o.status = 'created' OR u_p_o.status='confirmed'")
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    /**
     * Get partner pay out history
     *
     * @param $userId
     * @return mixed
     */
    public function getUserShortHistory($userId)
    {
        $select = $this->select()
            ->from(
                array('u_p_o' => self::$_tableName),
                array('u_p_o.id', 'u_p_o.created', 'u_p_o.sum', 'u_p_o.status')
            )
            ->where("u_p_o.user_id = '$userId'")
            ->limit(3)
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }
}