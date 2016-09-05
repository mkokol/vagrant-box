<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        Zend_Registry::set(
            'config',
            new Zend_Config($this->getOptions())
        );
    }

    protected function _initAutoloaders()
    {
        Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
        Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(false);
    }

    protected function _initAppBasic()
    {
        Helpers_General_ControllerAction::initStartTime();
    }

    protected function _initDBConnection()
    {
        $this->bootstrap('db');
    }

    protected function _initSession()
    {
        Session::getInstance()->initUserData();
        Session::getInstance()->initMemcached();
    }

    protected function _initRequest(array $options = array())
    {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        $front->setRequest(new Zend_Controller_Request_Http());
        $front->setBaseUrl(Helpers_General_ControllerAction::getBaseUrl());
    }

    protected function _initRouts()
    {
        Zend_Controller_Front::getInstance()->registerPlugin(new Helpers_General_UrlManager());
    }
}
