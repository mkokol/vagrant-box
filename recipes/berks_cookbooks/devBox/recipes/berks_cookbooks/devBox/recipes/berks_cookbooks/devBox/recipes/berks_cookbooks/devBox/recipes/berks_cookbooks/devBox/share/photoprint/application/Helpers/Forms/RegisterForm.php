<?php

class Helpers_Forms_RegisterForm extends Helpers_Forms_GenForm
{

    public function __construct($defaultCaptcha = '')
    {
        parent::__construct('forms/registration');

        $base_url = $this->request->getBaseUrl();

        // add main form config
        $this->setName('registration');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/user/registration');

        // add main form elements
        $userName = $this->createElement('text', 'user_name');
        $userName->addValidator('NotEmpty', false)
            ->setRequired(true)
            ->setLabel($this->t->_('user_name') . ' :');

        $password = $this->createElement('password', 'password');
        $password->setLabel($this->t->_('password') . ' :')
            ->addValidator('StringLength', false, array(3, 50))
            ->addValidator('Identical', false, array($this->request->getPost('confirmPassword')))
            ->setRequired(true)
            ->setValue('');

        $confirmPassword = $this->createElement('password', 'confirmPassword');
        $confirmPassword->setLabel($this->t->_('confirm_password') . ' :')
            ->addValidator('StringLength', false, array(3, 50))
            ->addValidator('Identical', false, array($this->request->getPost('password')))
            ->setRequired(true)
            ->setValue('');
        $val = $confirmPassword->getValidator('Identical');
        $val->setMessage($this->t->_('not_same'), Zend_Validate_Identical::NOT_SAME);

        $email = $this->createElement('text', 'email');
        $email->setLabel($this->t->_('email') . ' :')
            ->addFilter('StringToLower')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress')
            ->addValidator(new Helpers_Validator_UniqueEmail());

        $phone = $this->createElement('text', 'phone');
        $phone->setLabel($this->t->_('phone') . ' :')
            ->addValidator('regex', false, array('/^[+]{0,1}+[0-9|(|)]*$/'));
        $val = $phone->getValidator('regex');
        $val->setMessage($this->t->_('not_match'), Zend_Validate_Regex::NOT_MATCH);

        $captchaImage = new Zend_Captcha_Image();
        $captchaImage->setTimeout('300')
            ->setWordLen(rand(3, 5))
            ->setHeight('50')
            ->setWidth('140')
            ->setFont('public/captcha/font/CALIBRIB.TTF') // path to your font
            ->setImgDir('public/captcha/images') // where the image is stored
            ->setImgUrl($base_url . '/public/captcha/images')
            ->setExpiration('10');

        $captcha = new Zend_Form_Element_Captcha('captcha', array('captcha' => $captchaImage));
        $captcha->setLabel($this->t->_('captcha') . ' :')
            ->addValidator('NotEmpty', true);
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->t->_('save_btn'));
        $submit->setAttrib('class', 'button');

        $fieldList = [
            $userName,
            $email,
            $password,
            $confirmPassword,
            $phone
        ];

        if ($defaultCaptcha !== 'NO_CAPTCHA-super_captcha_hack') {
            $fieldList[] = $captcha;
        }

        $fieldList[] = $submit;

        $this->addElements($fieldList);
    }

}
