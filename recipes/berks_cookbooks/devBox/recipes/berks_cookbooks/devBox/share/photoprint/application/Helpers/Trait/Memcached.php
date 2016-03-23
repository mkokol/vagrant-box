<?php

trait Helpers_Trait_Memcached
{
    public function initMemcached()
    {
        $config = Zend_Registry::get('config');

        $cacheBackend = new Zend_Cache_Backend_Memcached(
            [
                'servers'       => [
                    [
                        'host' => $config->memcached->host,
                        'port' => $config->memcached->port
                    ]
                ],
                'compression'   => true,
                'compatibility' => true
            ]
        );

        $cacheFrontend = new Zend_Cache_Core(
            [
                'caching'                 => true,
                'cache_id_prefix'         => 'photoprint_',
                'write_control'           => true,
                'automatic_serialization' => true,
                'ignore_user_abort'       => true,
            ]
        );

        // Build a caching object
        $memcache = Zend_Cache::factory($cacheFrontend, $cacheBackend);
        Zend_Registry::set('memcache', $memcache);
    }

    public function isCached($recordId)
    {
        return false;

        /** @var Zend_Cache_Core */
        $memcache = Zend_Registry::get('memcache');

        return $memcache->test($recordId);
    }

    public function saveInCache($data, $recordId)
    {
        /** @var Zend_Cache_Core */
        $memcache = Zend_Registry::get('memcache');

        return $memcache->save($data, $recordId);
    }

    public function loadFromCache($recordId)
    {
        /** @var Zend_Cache_Core */
        $memcache = Zend_Registry::get('memcache');

        return $memcache->load($recordId);
    }
}
