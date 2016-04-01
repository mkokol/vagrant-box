<?php

class InfoController extends Helpers_General_ControllerAction
{

    /**
     * payment and transportation details page
     */
    public function paymentAction()
    {
        $this->loadTranslation('info/payment');
    }

    /**
     * payment and transportation details page
     */
    public function transportationAction()
    {
        $this->loadTranslation('info/transportation');
    }

    /**
     * help page show how to make order
     */
    public function helpAction()
    {
        $this->loadTranslation('produces/warranty');
        $this->loadTranslation('info/help');
    }

    public function designersAction()
    {
        $this->loadTranslation('info/designers');
    }

    public function developersAction()
    {
        $this->loadTranslation('info/developers');
    }

    public function businessAction()
    {
        $this->loadTranslation('info/business');
    }

}
