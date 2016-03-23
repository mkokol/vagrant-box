<?php

class Helpers_Forms_Payment extends Helpers_Forms_GenForm
{
    private $session;

    public function __construct($session)
    {
        parent::__construct('forms/payment');
        $this->session = $session;
        $base_url = $this->request->getBaseUrl();
        // add main form config
        $this->setName('payment');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/user/create-order?items=' .$this->session->itemSelected);
        $current_form = new Zend_Form_Element_Hidden('currentForm');
        $current_form->setValue('1');
        $current_form->setLabel('1');
        $question = new Zend_Form_Element_Radio('paymentType');
        $question->setRequired(true)
            ->setLabel($this->t->_('payment_sys'))
            ->setMultiOptions(
                array(
                    'privatbank' => ' ' . $this->t->_('privatbank'),
//                    'creditcart' => ' ' . $this->t->_('creditcart'),
//                    'cardtransfer' => ' ' . $this->t->_('cardtransfer'),
                    'webmoney' => ' ' . $this->t->_('web_money'),
                    'afterreceive' => ' ' . $this->t->_('after_receive')
                )
            );

        if (($this->request->getPost('currentForm') == '2') && ($this->request->getParam('type', '') == 'back')) {
            $createOrder = new Zend_Session_Namespace('create-order');
            $question->setValue($createOrder->paymentType);
        }
        $this->addElements(array($current_form, $question));
        $this->addCustomDecorator();
    }

    public function isValid($data)
    {
        $isValid = parent::isValid($data);
        if($isValid){
            $this->session->paymentType = $data['paymentType'];
        }
        return $isValid;
    }
}
