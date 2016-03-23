<?php

class Helpers_View_Url extends Helpers_View_H
{

    public function build($path, $params = [], $lang = null)
    {
        if (strpos($path, '/ru/') !== false || strpos($path, '/ua/') !== false) {
            $path = '/' . preg_replace('/^\/(ru|ua)\//', '', $path, 1);
        }

        $lang = (!$lang) ? $this->lang : $lang;

        $param = '';
        foreach ($params as $key => $value) {
            $param .= ($param == '') ? "?$key=$value" : "&$key=$value";
        }

        return $this->baseUrl . '/' . $lang . $path . $param;
    }

    public function seo($path, $params = [])
    {
        $url = $this->baseUrl . $path;
        $param = '';

        foreach ($params as $key => $value) {
            $param .= ($param == '') ? "?$key=$value" : "&$key=$value";
        }

        return $url . $param;
    }

    public function buildFullUrl($path, $params = [], $lang = null)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $domainWithSchema =  $request->getScheme() . '://' . $request->getHttpHost();

        return $domainWithSchema . $this->build($path, $params, $lang);
    }
}
