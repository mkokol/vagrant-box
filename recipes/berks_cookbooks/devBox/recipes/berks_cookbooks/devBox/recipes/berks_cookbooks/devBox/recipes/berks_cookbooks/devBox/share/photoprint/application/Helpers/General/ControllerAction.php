<?php

class Helpers_General_ControllerAction extends Zend_Controller_Action
{
    use Helpers_General_Traits_Static;
    use Helpers_General_Traits_Translation;
    use Helpers_General_Traits_Breadcrumbs;
    use Helpers_General_Traits_Xml;
    use Helpers_General_Traits_Email;

    public static $currentRouteId = null;
    private static $startTime = null;

    protected $refKey = null;

    public function __call($name, $args)
    {
        $this->error404();
    }

    /**
     * TODO: Please refactor me
     *
     * @throws Zend_Exception
     */
    public function init()
    {
        $this->initRefKey();
        $config = Zend_Registry::get('config');

        $this->view->controller = $this->_request->getControllerName();
        $this->view->action = $this->_request->getActionName();

        // language link
        $requestUrlSEO = ($this->_request->getBaseUrl() != '' && strlen($this->_request->getBaseUrl()) == 2)
            ? preg_replace('/^\\' . $this->_request->getBaseUrl() . '/', '', $this->_request->getRequestUri(), 1)
            : $this->_request->getRequestUri();

        $lang = Helpers_General_UrlManager::getLanguage();
        $oppositeLang = ($lang == 'ru') ? 'ua' : 'ru';
        $lastLinks = [];
        $lastLink = '';

        $seoRoute = new SeoRoute();
        $requestUrlSEOLanguage = $seoRoute->fetchLanguageSEORows($lang, $requestUrlSEO);

        if (is_array($requestUrlSEOLanguage)) {
            foreach ($requestUrlSEOLanguage as $urlSEOLanguage) {
                $lastLinks[$urlSEOLanguage['lang']] = $urlSEOLanguage;
            }
        }

        if (count($lastLinks) == 2) {
            $lastLink = $lastLinks[$oppositeLang]['seo_url'];
        } elseif (isset($lastLinks[$lang]) && count($lastLinks) == 1) {
            $lastLink = '/' . $lastLinks[$lang]['controller'] . '/' . $lastLinks[$lang]['action'];
            if ($lastLinks[$lang]['param1_name']) {
                $lastLink .= '/' . $lastLinks[$lang]['param1_name'] . '/' . $lastLinks[$lang]['param1_val'];
            }
            if ($lastLinks[$lang]['param2_name']) {
                $lastLink .= '/' . $lastLinks[$lang]['param2_name'] . '/' . $lastLinks[$lang]['param2_val'];
            }
            if ($lastLinks[$lang]['get1_name'] || $lastLinks[$lang]['get2_name']) {
                $lastLink .= '?';
            }
            if ($lastLinks[$lang]['get1_name']) {
                $lastLink .= $lastLinks[$lang]['get1_name'] . '=' . $lastLinks[$lang]['get1_val'];
            }
            if ($lastLinks[$lang]['get1_name'] && $lastLinks[$lang]['get2_name']) {
                $lastLink .= '&';
            }
            if ($lastLinks[$lang]['get2_name']) {
                $lastLink .= $lastLinks[$lang]['get2_name'] . '=' . $lastLinks[$lang]['get2_val'];
            }
        } elseif (isset($lastLinks[$oppositeLang]) && count($lastLinks) == 1) {
            $lastLink = '/' . explode("/$oppositeLang/", $lastLinks[$oppositeLang]['seo_url'])[1];
        } elseif (strpos($this->_request->getRequestUri(), $lang)) {
            $lastLink = (false != strpos($this->_request->getRequestUri(), "/$lang/"))
                ? '/' . explode("/$lang/", $this->_request->getRequestUri())[1]
                : '/';
        }

        $this->view->lastLink = preg_replace('/(.)*\/$/', '$1', $lastLink);
        $this->view->requestFullUri = $this->_request->getScheme() . '://' . $this->_request->getHttpHost() . $this->_request->getRequestUri();

        $this->view->q = $this->_request->getQuery('q', '');

        // - language link -----
        $this->initViewParams($config);
        $this->initViewBasicJsCssTranslation($config);
        $this->initSeo();
    }

    public function initViewParams($config)
    {
        $language = Helpers_General_UrlManager::getLanguage();
        $this->view->baseUrl = Helpers_General_ControllerAction::getBaseUrl();
        $this->view->version = $config->version;
        $this->view->language = $language;
        $this->view->languageList = $languageList = explode(',', trim($config->languages->list));
        $this->view->supportLanguage = ($language == 'ua') ? 'uk' : $language;

        if (in_array('en', $languageList)) {
            $userSession = new Zend_Session_Namespace('UserSession');
            if ($userSession->supportLanguage === true) {
                $this->view->supportLanguage = 'en';
            }
        }

        $this->view->tracking = (bool)$config->tracking;
        $this->view->islogin = false;

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return;
        }

        $this->view->islogin = true;
        $this->view->isAdmin = false;
        $userData = Zend_Auth::getInstance()->getIdentity();

        if ($userData->permission == 'admin') {
            $this->view->tracking = false;
            $this->view->isAdmin = true;

            $userSession = new Zend_Session_Namespace('UserSession');
            $this->view->editSiteSettings = $userSession->editSiteSettings;
        }
        $this->view->isEditSiteSettingsOn = $this->isEditSiteSettingsOn();
    }

    protected function isEditSiteSettingsOn()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        $userData = Zend_Auth::getInstance()->getIdentity();

        if ($userData->permission != 'admin') {
            return false;
        }

        $userSession = new Zend_Session_Namespace('UserSession');

        return $userSession->editSiteSettings;
    }

    public function initViewBasicJsCssTranslation($config)
    {
        if (!$this->_request->isXmlHttpRequest()) {
            $this->appendStylesheet('main', true);
            $this->appendFile('main.min', true);
            $this->view->basketItemsCount = Baskets::getMyItemCounts();
        }

        $this->loadTranslation('general');
        $this->view->addHelperPath($config->viewHelper->path, $config->viewHelper->prefix);

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->topMenuItems = Users::getUserMenuItems();
            $this->loadTranslation('user_menu');
        }
    }

    private function initSeo()
    {
        if (!self::$currentRouteId) {
            return;
        }

        $seoContents = new SeoContents();
        $seo = $seoContents->getContent(self::$currentRouteId);

        if ($seo) {
            $this->view->seo = $seo;
        }
    }

    public static function initStartTime()
    {
        self::$startTime = microtime(true);
    }

    public static function getLoadTime()
    {
        return microtime(true) - self::$startTime;
    }

    public function removeDefaultView()
    {
        Zend_Controller_Action_HelperBroker::removeHelper('viewRenderer');
    }

    public function viewJson($data, $code = 200)
    {
        $output = Zend_Json::encode($data);
        $response = $this->getResponse();

        if ($code === 404) {
            $response->setRawHeader('HTTP/1.1 404 Not Found');
        }

        $response->setBody($output)->setHeader('content-type', 'application/json', true);
    }

    public function postDispatch()
    {
        parent::postDispatch();

        $this->appendLoadedTranslation();
        $this->view->breadcrumbs = $this->breadcrumbs;

        $userSession = new Zend_Session_Namespace('UserSession');
        $userSession->previousUrl =
            $this->_request->getScheme() . '://' . $this->_request->getHttpHost() . $this->_request->getRequestUri();
    }

    public function redirectTo($url, array $options = [])
    {
        $language = Helpers_General_UrlManager::getLanguage();

        $this->redirect("$language/$url");
    }

    public function error404()
    {
        header('HTTP/1.0 404 Not Found');
        $this->loadTranslation('error/error');
        $this->view->t = Helpers_General_ControllerAction::getLoadedTranslation();
        $this->renderScript('exception/error.phtml');
    }

    public static function getBaseUrl()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        if (strpos($baseUrl, '/index.php') !== false) {
            $baseUrl = str_replace('/index.php', '', $baseUrl);
        }

        return $baseUrl;
    }

    protected function login()
    {
        $result = [];
        $email = strtolower($this->_request->getParam('email'));
        $password = $this->_request->getParam('password');
        $result['status'] = (Users::login($email, $password)) ? 'success' : 'failed';

        return $result;
    }

    // move to entity without redirect
    protected function validateProductGroup($group)
    {
        if (!$group) {
            $this->redirectTo('error/error');
        }

        $productsGroup = new ProductsGroup();

        if (!$productsGroup->exist($group)) {
            $this->redirectTo('error/error');
        }
    }

    private function initRefKey()
    {
        $refKey = $this->_request->getCookie('refKey', null);

        if ($refKey != null) {
            $this->refKey = $refKey;

            return;
        }

        $refKey = $this->_request->getParam('refKey', null);

        if ($refKey == null) {
            return;
        }

        setcookie('refKey', $refKey, time() + 3600 * 24 * 356 * 3, '/');
        $this->refKey = $refKey;
    }
}
