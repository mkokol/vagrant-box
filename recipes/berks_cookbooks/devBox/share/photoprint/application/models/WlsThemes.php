<?php

class WlsThemes extends Model
{

    public static $_tableName = 'wls_themes';

    public function getThemes($wlId, $fields = '*')
    {
        $select = $this->select()
            ->from(array('wl_t' => self::$_tableName), $fields)
            ->where("wl_t.wl_id = '$wlId'")
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    public function getThemesByWLCode($wlCode, $fields = '*')
    {
        $select = $this->select()
            ->from(array('wl_t' => self::$_tableName), $fields)
            ->join(array('wl' => Wls::$_tableName), 'wl.id = wl_t.wl_id', array())
            ->where("wl.code = '$wlCode'")
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

}