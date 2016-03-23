<?php

class Helpers_Forms_ResendPassword extends Helpers_Forms_GenForm
{
    public function __construct()
    {
        parent::__construct('forms/resendpassword');

        $base_url = $this->request->getBaseUrl();
        // add main form config
        $this->setName('resendPassword');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/user/resend-password');
        // add main form elements

        $email = $this->createElement('text', 'email');
        $email->setLabel($this->t->_('email') . ' :')
            ->addFilter('StringToLower')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress');

        $captchaImage = new Zend_Captcha_Image();
        $captchaImage->setTimeout('300')
            ->setWordLen(rand(3, 5))
            ->setHeight('50')
            ->setWidth('140')
            // path to your font
            ->setFont('public/captcha/font/CALIBRIB.TTF')
            // where the image is stored
            ->setImgDir('public/captcha/images')
            ->setImgUrl($base_url . '/public/captcha/images');
        $captcha = new Zend_Form_Element_Captcha('captcha', ['captcha' => $captchaImage]);
        $captcha->setLabel($this->t->_('captcha') . ' :')
            ->addValidator('NotEmpty', true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->t->_('send_btn'));
        $submit->setAttrib('class', 'button');

        $this->addElements([$email, $captcha, $submit]);
    }
}