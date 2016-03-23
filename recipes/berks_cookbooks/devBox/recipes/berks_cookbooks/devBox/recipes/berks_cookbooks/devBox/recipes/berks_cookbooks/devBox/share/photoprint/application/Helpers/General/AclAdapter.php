<?php

class Helpers_General_AclAdapter extends Zend_Acl
{

    public function __construct()
    {
        $this->addResource(new Zend_Acl_Resource('index'));
        $this->addResource(new Zend_Acl_Resource('info'));
        $this->addResource(new Zend_Acl_Resource('error'));
        $this->addResource(new Zend_Acl_Resource('admin'));
        $this->addResource(new Zend_Acl_Resource('produces'));
        $this->addResource(new Zend_Acl_Resource('user'));
        $this->addResource(new Zend_Acl_Resource('partnership'));
        $this->addResource(new Zend_Acl_Resource('wl'));
        $this->addResource(new Zend_Acl_Resource('update'));
        $this->addResource(new Zend_Acl_Resource('api'));
        $this->addResource(new Zend_Acl_Resource('public'));
        $this->addResource(new Zend_Acl_Resource('seofiles'));
        $this->addResource(new Zend_Acl_Resource('php-test'));
        $this->addResource(new Zend_Acl_Resource('statistic'));
        $this->addResource(new Zend_Acl_Resource('search'));
        $this->addResource(new Zend_Acl_Resource('crawler'));


        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('member'), 'guest');
        $this->addRole(new Zend_Acl_Role('admin'), 'member');

        $this->allow('guest', 'index');
        $this->allow('guest', 'info');
        $this->allow('guest', 'error');
        $this->allow('guest', 'produces');
        $this->allow('guest', 'user');
        $this->allow('guest', 'api');
        $this->allow('guest', 'public');
        $this->allow('guest', 'seofiles');
        $this->allow('guest', 'php-test');
        $this->allow('guest', 'search');
        $this->deny('guest', 'user', ['home']);
        $this->deny('guest', 'update');
        $this->deny('guest', 'search', ['reindex']);

        $this->allow('member', 'user', ['home']);
        $this->allow('member', 'partnership');
        $this->allow('member', 'wl');

        $this->allow('admin', 'admin');
        $this->allow('admin', 'update');
        $this->allow('admin', 'statistic');
        $this->allow('admin', 'crawler');
        $this->allow('admin', 'search', ['reindex']);
    }
}
