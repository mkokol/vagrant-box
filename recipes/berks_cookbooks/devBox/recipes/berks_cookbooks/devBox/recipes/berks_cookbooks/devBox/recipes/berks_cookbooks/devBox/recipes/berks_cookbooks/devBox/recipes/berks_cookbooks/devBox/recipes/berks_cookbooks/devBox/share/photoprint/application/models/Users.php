<?php

class Users extends Model
{

    public static $_tableName = 'users';

    public static function getUserMenuItems()
    {
        $userMenuItems = array(
            array('name' => 'user_menu_my_info', 'url' => '/user/info'),
            array('name' => 'user_menu_my_photo', 'url' => '/user/gallery'),
            array('name' => 'user_menu_basket', 'url' => '/user/basket'),
            array('name' => 'user_menu_partnership', 'url' => '/partnership')
        );
        $auth = Zend_Auth::getInstance();
        $adminMenuItems = null;
        if ($auth->hasIdentity()) {
            $userDate = $auth->getIdentity();
            if(isset($userDate->wl) && $userDate->wl){
                $userMenuItems = array_merge($userMenuItems, array(array('name' => 'wl_menu', 'url' => '/wl')));
            }
            if ($userDate->permission == 'admin') {
                $adminMenuItems = array(
                    array('name' => 'user_menu_orders', 'url' => '/admin/orders'),
                    array('name' => 'user_menu_photo', 'url' => '/admin/photo'),
                    array('name' => 'user_menu_themes', 'url' => '/admin/theme-list'),
                    array('name' => 'user_menu_tags', 'url' => '/admin/tag-list'),
                    array('name' => 'user_menu_categories', 'url' => '/admin/categories'),
                    array('name' => 'user_menu_products', 'url' => '/admin/product-list'),
                    array('name' => 'user_menu_payout', 'url' => '/admin/payout')
                );
            }
        }
        return array('userMenu' => $userMenuItems, 'adminMenu' => $adminMenuItems);
    }

    public static function getCarrentUserId()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userDate = $auth->getIdentity();
            $userId = $userDate->id;
        } else {
            $userId = Session::getSessionId();
        }
        return $userId;
    }

    public static function login($email, $password)
    {
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('users');
        $authAdapter->setIdentityColumn('email');
        $authAdapter->setCredentialColumn('password');
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential(md5($password));
        $auth = Zend_Auth::getInstance();
        $auth_result = $auth->authenticate($authAdapter);
        if ($auth_result->isValid()) {
            $data = $authAdapter->getResultRowObject();
            Session::getInstance()->setData(array('auth' => $data));
            $auth->getStorage()->write($data);
            return true;
        }
        return false;
    }

    public static function checkLogin($value)
    {
        $users = new Users();
        $userList = $users->fetchAll("user_name = '$value'")->toArray();
        if (count($userList) > 0) {
            return $userList[0]['id'];
        }
        return 0;
    }

    public static function getUserIdByHash($value)
    {
        $users = new Users();
        $userList = $users->fetchAll("hash = '$value'")->toArray();
        if (count($userList) === 1) {
            return $userList[0]['id'];
        }
        return 0;
    }

    public static function getInfoByEmail($value)
    {
        $users = new Users();
        $userList = $users->fetchAll("email = '$value'")->toArray();
        if (count($userList) > 0) {
            return $userList[0];
        }
        return 0;
    }

    public function getInfoById($id)
    {
        $select = $this->select()
            ->from(array('u' => self::$_tableName), '*')
            ->joinLeft(array('i' => Images::$_tableName), 'u.avatar_id = i.id', array('name', 'extension'))
            ->where("u.id = '$id'")
            ->setIntegrityCheck(false);
        $users = $this->fetchAll($select);
        return (isset($users[0])) ? $users[0] : null;
    }

    public static function checkEmail($value)
    {
        $users = new Users();
        $userList = $users->fetchAll("email = '$value'")->toArray();
        if (count($userList) > 0) {
            return $userList[0]['id'];
        }
        return 0;
    }

    public function acceptUserShopRules()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userDate = $auth->getIdentity();
            $shopCode = substr(md5($userDate->id . $userDate->created), 0, 9);
            $this->update(
                array(
                    'shop_code' => $shopCode,
                    'accept_shop_rules' => 1
                ),
                "id = '$userDate->id'"
            );
            $userDate->shop_code = $shopCode;
            $userDate->accept_shop_rules = 1;
            return true;
        }
        return false;
    }
}
