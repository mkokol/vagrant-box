<?php

class Session extends Model
{
    public static $_tableName = 'session';
    private static $object = null;
    private static $psession = '';
    private $sessionData = '';

    public static function getInstance()
    {
        if (self::$object === null) {
            self::$object = new Session();
        }
        return self::$object;
    }

    public static function getSessionId()
    {
        return self::$psession;
    }

    /**
     * Initialize user data from session or from database
     */
    public function initUserData()
    {
        if (!isset($_COOKIE['psession'])) {
            $sesCode = md5(time());
            setcookie('psession', $sesCode, time() + 3600 * 24 * 30 * 12 * 3, '/');
            $_COOKIE['psession'] = $sesCode;
        }
        self::$psession = $_COOKIE['psession'];
        $data = $this->getData();
        $auth = Zend_Auth::getInstance();
        if (isset($data['auth']) && is_object($data['auth']) && (!$auth->hasIdentity())) {
            $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName('users');
            $authAdapter->setIdentityColumn('email');
            $authAdapter->setCredentialColumn('password');
            $authAdapter->setIdentity($data['auth']->email);
            $authAdapter->setCredential($data['auth']->password);
            $auth->getStorage()->write($data['auth']);
        }
    }

    public function getData()
    {
        if ($this->sessionData != '') {
            return $this->sessionData;
        } else if (self::$psession != '') {
            $data = $this->fetchAll("code = '" . self::$psession . "'")->toArray();
            if (count($data) == 1) {
                $this->sessionData = unserialize($data[0]['data']);
                return $this->sessionData;
            }
        }
        return null;
    }

    public function setData($data)
    {
        if (($prevData = $this->getData()) == null) {
            $this->sessionData = $data;
            $insertData = array(
                'code'    => self::$psession,
                'data'    => serialize($this->sessionData),
                'created' => date('Y-m-d H:i:s')
            );
            $this->insert($insertData);
        } else {
            $this->sessionData = array_merge($prevData, $data);
            $insertData = array(
                'data'        => serialize($this->sessionData),
                'last_update' => date('Y-m-d H:i:s')
            );
            $this->update($insertData, "code = '" . self::$psession . "'");
        }
    }
}
