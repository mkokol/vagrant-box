<?php

class Helpers_Forms_UpdateUserInfo extends Helpers_Forms_GenForm
{

    public function __construct()
    {
        parent::__construct('forms/updateuser');

        // add main form config
        $this->setName('update');
        $this->setMethod('post');

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user_date = $auth->getIdentity();
            $users = new Users();
            $user = $users->fetchAll("id = $user_date->id")->toArray();
            $user = $user[0];
        }

        $userName = $this->createElement('text', 'user_name');
        $userName->addValidator('NotEmpty', false)
            ->setRequired(true)
            ->setLabel($this->t->_('user_name') . ' :')
            ->setValue((isset($user['user_name'])) ? $user['user_name'] : '');
        $email = $this->createElement('text', 'email');
        $email->setLabel($this->t->_('email') . ' :')
            ->addFilter('StringToLower')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress')
            ->setValue((isset($user['email'])) ? $user['email'] : '');
        $phone = $this->createElement('text', 'phone');
        $phone->setLabel($this->t->_('phone') . ' :')
            ->addValidator('regex', false, array('/^[+]{0,1}+[0-9|(|)]*$/'))
            ->setValue((isset($user['phone'])) ? $user['phone'] : '');
        $val = $phone->getValidator('regex');
        $val->setMessage($this->t->_('not_match'), Zend_Validate_Regex::NOT_MATCH);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->t->_('save_btn'));
        $submit->setAttrib('class', 'button');

        $this->addElements(
            array(
                $userName,
                $email,
                $phone,
                $submit
            )
        );
    }

    public function validateAndSave($formData)
    {
        if ($this->isValid($formData)) {
            $users = new Users();
            $users->update(
                array(
                    'user_name' => $formData['user_name'],
                    'email' => $formData['email'],
                    'phone' => $formData['phone'],
                ),
                'id = ' . Users::getCarrentUserId()
            );
            return true;
        }
        return false;
    }

}