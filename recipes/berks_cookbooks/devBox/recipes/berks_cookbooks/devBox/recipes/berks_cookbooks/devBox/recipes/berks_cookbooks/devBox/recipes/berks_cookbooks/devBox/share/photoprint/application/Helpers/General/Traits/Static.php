<?php

trait Helpers_General_Traits_Static
{
    private static $headStylesheet = array();
    private static $headFile = array();

    public function appendStylesheet($cssObj, $addVersion = false)
    {
        $config = Zend_Registry::get('config');
        if (is_array($cssObj)) {
            foreach ($cssObj as $file) {
                if ($addVersion) {
                    self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/stylesheets/' . $file . '.' . $config->version . '.css';
                } else {
                    self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/stylesheets/' . $file . '.css?v=' . $config->version;
                }
            }
        } else {
            if ($addVersion) {
                self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/stylesheets/' . $cssObj . '.' . $config->version . '.css';
            } else {
                self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/stylesheets/' . $cssObj . '.css?v=' . $config->version;
            }
        }
    }

    public function appendFile($jsObj, $addVersion = false)
    {
        $config = Zend_Registry::get('config');
        if (is_array($jsObj)) {
            foreach ($jsObj as $file) {
                if ($addVersion) {
                    self::$headFile[] = $this->_request->getBaseUrl() . '/public/scripts/' . $file . '.' . $config->version . '.js';
                } else {
                    self::$headFile[] = $this->_request->getBaseUrl() . '/public/scripts/' . $file . '.js?v=' . $config->version;
                }
            }
        } else {
            if ($addVersion) {
                self::$headFile[] = $this->_request->getBaseUrl() . '/public/scripts/' . $jsObj . '.' . $config->version . '.js';
            } else {
                self::$headFile[] = $this->_request->getBaseUrl() . '/public/scripts/' . $jsObj . '.js?v=' . $config->version;
            }
        }
    }

    public function appendWidgets($widgetObj, $addVersion = false)
    {
        $config = Zend_Registry::get('config');
        if (is_array($widgetObj)) {
            foreach ($widgetObj as $widget) {
                if ($widget != 'featureList') {
                    if ($addVersion) {
                        self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widget . '/style.' . $config->version . '.css';
                    } else {
                        self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widget . '/style.css?v=' . $config->version;
                    }
                }

                if ($addVersion) {
                    self::$headFile[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widget . '/code.min.' . $config->version . '.js';
                } else {
                    self::$headFile[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widget . '/code.min.js?v=' . $config->version;
                }

            }
        } else {
            if ($widgetObj != 'featureList') {
                if ($addVersion) {
                    self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widgetObj . '/style.' . $config->version . '.css';
                } else {
                    self::$headStylesheet[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widgetObj . '/style.css?v=' . $config->version;
                }
            }
            if ($addVersion) {
                self::$headFile[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widgetObj . '/code.min.' . $config->version . '.js';
            } else {
                self::$headFile[] = $this->_request->getBaseUrl() . '/public/widgets/' . $widgetObj . '/code.min.js?v=' . $config->version;
            }
        }
    }

    public function clearStylesheet()
    {
        self::$headStylesheet = [];
    }

    public function clearFile()
    {
        self::$headFile = [];
    }

    public static function getAllStylesheet()
    {
        return self::$headStylesheet;
    }

    public static function getAllFile()
    {
        return self::$headFile;
    }
}
