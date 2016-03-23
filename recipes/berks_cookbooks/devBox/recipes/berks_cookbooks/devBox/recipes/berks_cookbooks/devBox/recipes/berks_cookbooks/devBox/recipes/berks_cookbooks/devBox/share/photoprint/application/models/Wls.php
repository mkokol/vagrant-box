<?php

class Wls extends Model
{

    public static $_tableName = 'wls';
    public static $benefit = 25;

    /**
     * Get all client white labels
     *
     * @param $userId
     * @return array
     */
    public function getWlList($userId)
    {
        $select = $this->select()
            ->from(array('w' => self::$_tableName), '*')
            ->join(array('u_w' => UsersWls::$_tableName), 'w.id = u_w.wl_id', array())
            ->where('u_w.user_id = \'' . $userId . '\'')
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    /**
     * Get white label instance by code
     *
     * @param $code
     * @return null
     */
    public function getWlByCode($code)
    {
        $select = $this->select()
            ->from(array('w' => self::$_tableName), '*')
            ->where('w.code = \'' . $code . '\'')
            ->setIntegrityCheck(false);
        $wlList = $this->fetchAll($select)->toArray();
        return (isset($wlList[0])) ? $wlList[0] : null;
    }
}