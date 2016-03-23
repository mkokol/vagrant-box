<?php

class Helpers_General_UrlManager extends Zend_Controller_Plugin_Abstract
{
    private static $language;
    protected $role;
    protected $acl;

    public function __construct()
    {
        $auth = Zend_Auth::getInstance();
        $this->role = ($auth->hasIdentity()) ? $auth->getIdentity()->permission : 'guest';
        $this->acl = new Helpers_General_AclAdapter();
        $this->initRouting();
    }

    public static function getLanguage()
    {
        return self::$language;
    }

    public function initLanguage(Zend_Controller_Request_Abstract $request)
    {
        $config = Zend_Registry::get('config');
        self::$language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');

        if (!in_array(self::$language, explode(',', $config->languages->list))) {
            self::$language = $config->languages->default;
            $request->setControllerName('exception');
            $request->setActionName('error');

            return false;
        }

        return true;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $requetUri = $request->getRequestUri();

        if (strpos($requetUri, '/index.php') !== false) {
            $doamin = $_SERVER['SERVER_NAME'];
            $url = str_replace('/index.php', '', $requetUri);
            Header("HTTP/1.1 301 Moved Permanently");
            Header("Location: http://{$doamin}{$url}");
            exit;
        }

        if ($this->initLanguage($request)) {
            $frontController = Zend_Controller_Front::getInstance();
            $isDispatchable = $frontController->getDispatcher()->isDispatchable($request);

            if ($isDispatchable == true) {
                $controller = $request->controller;
                $action = $request->action;

                if (!$this->acl->isAllowed($this->role, $controller, $action)) {
                    $request->setControllerName('exception');
                    $request->setActionName('accessdenied');
                }
            } else {
                $request->setControllerName('exception');
                $request->setActionName('error');
            }
        }
    }

    public function initRouting()
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->setGlobalParam('lang', 'ru');
        $router->addRoute(
            'exception',
            new Zend_Controller_Router_Route(
                '/*',
                [
                    'lang'       => 'ru',
                    'controller' => 'error',
                    'action'     => 'error'
                ]
            )
        );
        $router->addRoute(
            'default',
            new Zend_Controller_Router_Route(
                '/',
                [
                    'lang'       => 'ru',
                    'controller' => 'index',
                    'action'     => 'redirect'
                ]
            )
        );
        $router->addRoute(
            'lang',
            new Zend_Controller_Router_Route(
                '/:lang',
                [
                    'controller' => 'index',
                    'action'     => 'index'
                ],
                [
                    'lang' => '(ru|ua)'
                ]
            )
        );
        $router->addRoute(
            'langController',
            new Zend_Controller_Router_Route(
                '/:lang/:controller',
                [
                    'action' => 'index'
                ]
            ),
            [
                'lang' => '(ru|ua)'
            ]
        );
        $router->addRoute(
            'langControllerAction',
            new Zend_Controller_Router_Route(
                '/:lang/:controller/:action',
                []
            ),
            [
                'lang' => '(ru|ua)'
            ]
        );
        $router->addRoute(
            'langControllerActionParams',
            new Zend_Controller_Router_Route(
                '/:lang/:controller/:action/*',
                []
            ),
            [
                'lang' => '(ru|ua)'
            ]
        );
        $router->addRoute(
            'api',
            new Zend_Controller_Router_Route(
                '/api/*',
                [
                    'lang'       => 'ru',
                    'controller' => 'api',
                    'action'     => 'public'
                ]
            ),
            [
                'lang' => '(api|ru|ua)'
            ]
        );
        $router->addRoute(
            'public',
            new Zend_Controller_Router_Route(
                '/public/products/*',
                [
                    'lang'       => 'ru',
                    'controller' => 'public',
                    'action'     => 'products'
                ]
            ),
            [
                'lang' => '(public|ru|ua)'
            ]
        );

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $requestUrlSEO = ($request->getBaseUrl() != '' && strlen($request->getBaseUrl()) == 2)
            ? preg_replace('/^\\' . $request->getBaseUrl() . '/', '', $request->getRequestUri(), 1)
            : $request->getRequestUri();
        $seoRoute = new SeoRoute();

        if (($seoRouteObj = $seoRoute->fetchSEORow($requestUrlSEO)) != null) {
            if($request->getQuery('tagId')) {
                $tagPageSeoRoute = $seoRoute->fetchSEORecord([
                    'lang' =>  $seoRouteObj['lang'],
                    'controller' => 'produces', 'action' => 'catalog',
                    'param1_name' => 'item', 'param1_val' => $seoRouteObj['param1_val'],
                    'param2_name' => '', 'param2_val' => '',
                    'get1_name' => $seoRouteObj['get1_name'], 'get1_val' => $seoRouteObj['get1_val'],
                    'get2_name' => 'tagId', 'get2_val' => $request->getQuery('tagId')
                ]);

                if($tagPageSeoRoute) {
                    $front = Zend_Controller_Front::getInstance();
                    $response = new Zend_Controller_Response_Http();
                    $response->setRedirect($request->getBaseUrl() . $tagPageSeoRoute['seo_url'], 301);
                    $front->setResponse($response);
                }
            }

            if ($seoRouteObj['url'] == $requestUrlSEO || $seoRouteObj['type'] == 'redirect') {
                $front = Zend_Controller_Front::getInstance();
                $response = new Zend_Controller_Response_Http();
                $response->setRedirect($request->getBaseUrl() . $seoRouteObj['seo_url'], 301);
                $front->setResponse($response);
            }

            $routParam = [
                'controller' => $seoRouteObj['controller'],
                'action'     => $seoRouteObj['action']
            ];
            if ($seoRouteObj['param1_name'] && $seoRouteObj['param1_val']) {
                $routParam[$seoRouteObj['param1_name']] = $seoRouteObj['param1_val'];
            }
            if ($seoRouteObj['param2_name'] && $seoRouteObj['param2_val']) {
                $routParam[$seoRouteObj['param2_name']] = $seoRouteObj['param2_val'];
            }
            if ($seoRouteObj['get1_name'] && $seoRouteObj['get1_val']) {
                Zend_Controller_Front::getInstance()->getRequest()->setQuery($seoRouteObj['get1_name'], $seoRouteObj['get1_val']);
            }
            if ($seoRouteObj['get2_name'] && $seoRouteObj['get2_val']) {
                Zend_Controller_Front::getInstance()->getRequest()->setQuery($seoRouteObj['get2_name'], $seoRouteObj['get2_val']);
            }
            if (strpos($seoRouteObj['seo_url'], '/ru/') !== false || strpos($seoRouteObj['seo_url'], '/ua/') !== false) {
                $pageRoutePath = '/:lang' . preg_replace('/^\/(ru|ua)/', '', $seoRouteObj['seo_url'], 1);
            } else {
                $routParam['lang'] = 'ru';
                $pageRoutePath = $seoRouteObj['seo_url'];
            }

            Helpers_General_ControllerAction::$currentRouteId = (isset($seoRouteObj['id'])) ? $seoRouteObj['id'] : null;

            $router->addRoute(
                'seoUrl',
                new Zend_Controller_Router_Route($pageRoutePath, $routParam)
            );
        } elseif($request->getQuery('themeId') && $request->getQuery('tagId')) {
            $requestUrlParams = array_values(array_filter(explode('/', explode('?', $requestUrlSEO)[0])));

            if (isset($requestUrlParams[4]) && $requestUrlParams[3] == 'item') {
                $tagPageSeoRoute = $seoRoute->fetchSEORecord([
                    'lang' =>  $requestUrlParams[0],
                    'controller' => 'produces', 'action' => 'catalog',
                    'param1_name' => 'item', 'param1_val' => $requestUrlParams[4],
                    'param2_name' => '', 'param2_val' => '',
                    'get1_name' => 'themeId', 'get1_val' => $request->getQuery('themeId'),
                    'get2_name' => '', 'get2_val' => ''
                ]);

                if($tagPageSeoRoute) {
                    $front = Zend_Controller_Front::getInstance();
                    $response = new Zend_Controller_Response_Http();
                    $response->setRedirect(
                        $request->getBaseUrl() . $tagPageSeoRoute['seo_url'] . '?tagId=' . $request->getQuery('tagId'),
                        301
                    );
                    $front->setResponse($response);
                }
            }
        }
    }
}
