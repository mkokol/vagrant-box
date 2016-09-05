<?php

class SeoContents extends Model
{
    public static $_tableName = 'seo_contents';

    public function getContent($routeId)
    {
        $select = $this->select()
            ->from(array('s_c' => self::$_tableName), '*')
            ->where('s_c.seo_route_id = ?', $routeId)
            ->setIntegrityCheck(false);

        $contents = $this->fetchAll($select);

        return (isset($contents[0])) ? $contents[0] : null;
    }
}
