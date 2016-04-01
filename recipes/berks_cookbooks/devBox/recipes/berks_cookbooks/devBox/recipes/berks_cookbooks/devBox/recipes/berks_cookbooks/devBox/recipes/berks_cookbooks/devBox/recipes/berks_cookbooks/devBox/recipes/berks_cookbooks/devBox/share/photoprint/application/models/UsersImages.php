<?php

class UsersImages extends Model
{

    public static $_tableName = 'users_images';
    public static $imagesOnPage = 4;

    /**
     * Get album images for page
     *
     * @param int $albumId
     * @param int $page
     * @return type
     */
    public function getAlbumImagesByPage($albumId = null, $page = 1)
    {
        $userId = Users::getCarrentUserId();
        $albumIdParam = ($albumId === null) ? 'u_i.album_id IS NULL' : "u_i.album_id = '$albumId'";
        $select = $this->select()
            ->from(array('u_i' => self::$_tableName), '*')
            ->join(array('i' => Images::$_tableName), 'i.id = u_i.image_id', array('img_name' => 'name', 'extension'))
            ->where("$albumIdParam AND u_i.user_id = '$userId'")
            ->order('u_i.id DESC')
            ->limit(self::$imagesOnPage, ($page - 1) * self::$imagesOnPage)
            ->setIntegrityCheck(false);
        $displayImages = $this->fetchAll($select)->toArray();
        $result = array();
        $baseUrl = Helpers_General_ControllerAction::getBaseUrl();
        for ($i = 0; $i < self::$imagesOnPage; $i++) {
            $result[] = array(
                'id' => (isset($displayImages[$i])) ? $displayImages[$i]['id'] : null,
                'path' => (isset($displayImages[$i]))
                    ? Images::getImagePath($baseUrl, $displayImages[$i]['img_name'], 's', $displayImages[$i]['extension'])
                    : $baseUrl . Images::$noImgPath
            );
        }
        return $result;
    }

    /**
     * Get album images page count
     *
     * @param type $albumId
     * @return type
     */
    public function getAlbumPageCount($albumId = null)
    {
        $userId = Users::getCarrentUserId();
        $albumIdParam = ($albumId === null) ? 'u_i.album_id IS NULL' : "u_i.album_id = '$albumId'";
        $select = $this->select()
            ->from(array('u_i' => self::$_tableName), array('count(*) as amount'))
            ->where("$albumIdParam AND u_i.user_id = '$userId'")
            ->setIntegrityCheck(false);
        $imageCountObj = $this->fetchAll($select);
        return ceil($imageCountObj[0]->amount / self::$imagesOnPage);
    }

    /**
     * Get all album images
     *
     * @param type $albumId
     * @return type
     */
    public function getAllAlbumImages($albumId = null)
    {
        $userId = Users::getCarrentUserId();
        $albumIdParam = ($albumId) ? "u_i.album_id = '$albumId'" : 'u_i.album_id IS NULL';
        $select = $this->select()
            ->from(array('u_i' => self::$_tableName), '*')
            ->join(array('i' => Images::$_tableName), 'i.id = u_i.image_id', array('img_name' => 'name', 'extension'))
            ->where("$albumIdParam AND u_i.user_id = '$userId'")
            ->order('u_i.id DESC')
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    public function isUsedImg($imgId)
    {
        $select = $this->select()
            ->from(array('u_i' => self::$_tableName), '*')
            ->where("u_i.image_id = '$imgId'")
            ->setIntegrityCheck(false);
        return (count($this->fetchAll($select)->toArray())) ? true : false;
    }

    /*   admin function   */

    public function getAllPhotosCount()
    {
        $select = $this->select()
            ->from(array('u_i' => self::$_tableName), array('count(*) as amount'))
            ->setIntegrityCheck(false);
        $imageCountObj = $this->fetchAll($select);
        return ceil($imageCountObj[0]->amount / self::$imagesOnPage);
    }

    public function getAllPhotosByPage($page = 1)
    {
        $select = $this->select()
            ->from(array('u_i' => UsersImages::$_tableName), '*')
            ->join(array('i' => Images::$_tableName), 'i.id = u_i.image_id', array('img_name' => 'name', 'extension'))
            ->order('id DESC')
            ->limit(self::$imagesOnPage, ($page - 1) * self::$imagesOnPage)
            ->setIntegrityCheck(false);
        return $this->fetchAll($select)->toArray();
    }

    public function hasNotSortedImages($userId)
    {
        $select = $this->select()
            ->from(array('u_i' => self::$_tableName), array('count(*) as amount'))
            ->where("album_id IS NULL AND u_i.user_id = '$userId'")
            ->setIntegrityCheck(false);
        $imageCountObj = $this->fetchAll($select);
        return ($imageCountObj[0]->amount) ? true : false;
    }

}