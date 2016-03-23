<?php

class Model extends Zend_Db_Table
{
    use Helpers_Trait_Memcached;

    public function __construct($config = array(), $definition = null)
    {
        $this->_name = static::$_tableName;
        parent::__construct($config, $definition);
    }
}
