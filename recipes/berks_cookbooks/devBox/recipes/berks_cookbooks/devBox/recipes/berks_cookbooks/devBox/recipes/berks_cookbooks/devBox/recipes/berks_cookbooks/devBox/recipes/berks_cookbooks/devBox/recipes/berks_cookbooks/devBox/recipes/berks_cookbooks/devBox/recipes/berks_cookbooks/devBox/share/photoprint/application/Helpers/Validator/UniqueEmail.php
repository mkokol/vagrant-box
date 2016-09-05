<?php

class Helpers_Validator_UniqueEmail extends Zend_Validate_Abstract
{
    const NOT_UNIQUE = "notUnique";

    protected $_messageTemplates = array(
        self::NOT_UNIQUE => "Email '%value%' has already been taken"
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        $userEmail = (Zend_Auth::getInstance()->hasIdentity())
            ? Zend_Auth::getInstance()->getIdentity()->email
            : '';
        $userId = Users::checkEmail($value);

        if ($userEmail != $value && $userId) {
            $this->_error(self::NOT_UNIQUE);

            return false;
        }

        return true;
    }

}