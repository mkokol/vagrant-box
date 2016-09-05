<?php

class UsersHashes extends Model
{
    public static $_tableName = 'users_hashes';

    public function getUserId($hash, $type, $status = 'new')
    {
        $select = $this->select()
            ->from(['u_h' => self::$_tableName], '*')
            ->where('hash = ?', $hash)
            ->where('type = ?', $type)
            ->where('status = ?', $status)
            ->setIntegrityCheck(false);
        $albums = $this->fetchAll($select)->toArray();

        return (count($albums) === 1) ? $albums[0]['user_id'] : null;
    }
}
