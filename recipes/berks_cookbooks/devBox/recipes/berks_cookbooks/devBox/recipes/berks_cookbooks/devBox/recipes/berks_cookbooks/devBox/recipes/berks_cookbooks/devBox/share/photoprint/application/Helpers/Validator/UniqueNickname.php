<?php

class Helpers_Validator_UniqueNickname extends Zend_Validate_Abstract
{

    const NOT_UNIQUE = "notUnique";

    protected $_messageTemplates = array(
        self::NOT_UNIQUE => "Username '%value%' has already been taken"
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        $isValid = true;
        $userId = Clients::checkLogin($value);
        if ($userId) {
            $this->_error(self::NOT_UNIQUE);
            $isValid = false;
        }
        return $isValid;
    }

}