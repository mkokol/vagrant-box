<?php

class Helpers_Forms_PartnerRegForm extends Helpers_Forms_GenForm
{

    public function __construct()
    {
        parent::__construct('forms/partnerreg');

        $base_url = $this->request->getBaseUrl();
        // add main form config
        $this->setName('registration');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/partner/registration');
        // add main form elements
        $name = $this->createElement('text', 'name');
        $name->addValidator('NotEmpty', false)
            ->setRequired(true)
            ->setLabel($this->t->_('name') . ' :');
        $surname = $this->createElement('text', 'surname');
        $surname->addValidator('NotEmpty', false)
            ->setRequired(true)
            ->setLabel($this->t->_('surname') . ' :');
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
            ->setRequired(true)
            ->addValidator('regex', false, array('/^[+]{0,1}+[0-9|(|)]*$/'));
        $val = $phone->getValidator('regex');
        $val->setMessage($this->t->_('not_match'), Zend_Validate_Regex::NOT_MATCH);

        $companyName = $this->createElement('text', 'companyName');
        $companyName->setLabel($this->t->_('company_name') . ' :');

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

        $this->addElements(array($email,
            $name,
            $surname,
            $password,
            $confirmPassword,
            $phone,
            $companyName,
            $captcha,
            $submit));
    }

}