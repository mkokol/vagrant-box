<?php

class Helpers_View_H extends Zend_View_Helper_Abstract
{

    public  $baseUrl = null;
    public $lang = null;
    protected $t = null;

    public function __call($name, $arguments)
    {
        $helperClass = "Helpers_View_" . ucfirst($name);
        $helper = new $helperClass();
        $helper->baseUrl = Helpers_General_ControllerAction::getBaseUrl();
        $helper->lang = Helpers_General_UrlManager::getLanguage();
        return $helper;
    }

    public function h()
    {
        return $this;
    }

}