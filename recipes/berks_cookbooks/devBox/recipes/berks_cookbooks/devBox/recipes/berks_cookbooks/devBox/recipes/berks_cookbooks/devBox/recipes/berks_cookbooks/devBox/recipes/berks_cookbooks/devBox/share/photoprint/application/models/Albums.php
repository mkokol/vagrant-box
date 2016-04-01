<?php

class Albums extends Model
{

    public static $_tableName = 'albums';

    /**
     * Get album name by id (for images album view)
     *
     * @param $albumId
     * @return string
     */
    public function getAlbumName($albumId)
    {
        $select = $this->select()
            ->from(array('a' => self::$_tableName), '*')
            ->where("id = '$albumId'")
            ->setIntegrityCheck(false);
        $albums = $this->fetchAll($select)->toArray();
        return (isset($albums[0])) ? $albums[0]['title'] : null;
    }


    public function getDefoultUuserAlbums($userId)
    {
        $select = $this->select()
            ->from(array('a' => self::$_tableName), '*')
            ->where("user_id = $userId")
            ->setIntegrityCheck(false);
        $albums = $this->fetchAll($select)->toArray();
        return (isset($albums[0])) ? $albums[0] : null;
    }

}