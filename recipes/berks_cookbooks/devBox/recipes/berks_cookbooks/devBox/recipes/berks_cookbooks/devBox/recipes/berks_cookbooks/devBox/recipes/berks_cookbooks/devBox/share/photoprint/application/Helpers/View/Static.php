<?php

class Helpers_View_Static extends Helpers_View_H
{

    public function path($name)
    {
        echo $this->baseUrl . '/public/theme/' . $name;
    }

    public function headCSS()
    {
        $stylesheet = Helpers_General_ControllerAction::getAllStylesheet();

        foreach ($stylesheet as $cssFile) {
            echo '<link href="' . $cssFile . '" rel="stylesheet">';
        }
    }

    public function headJS()
    {
        $files = Helpers_General_ControllerAction::getAllFile();

        foreach ($files as $jsFile) {
            echo '<script src="' . $jsFile . '" type="text/javascript"></script>';
        }
    }

    public function fullPath($name)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $domainWithSchema = $request->getScheme() . '://' . $request->getHttpHost();

        return $domainWithSchema . $this->baseUrl . '/public/theme/' . $name;
    }

    public function getImgBase64($name)
    {
        $path = 'public/theme/' . $name;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
