<?php

class ExceptionController extends Helpers_General_ControllerAction
{

    public function errorAction()
    {
        $this->error404();
    }

    public function accessdeniedAction()
    {
        $this->loadTranslation('error/accessdenied');
    }

}
