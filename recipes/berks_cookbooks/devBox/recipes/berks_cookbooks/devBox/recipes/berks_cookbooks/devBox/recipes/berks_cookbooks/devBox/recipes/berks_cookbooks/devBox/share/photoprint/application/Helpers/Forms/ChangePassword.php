<?php

class Helpers_Forms_ChangePassword extends Helpers_Forms_GenForm
{

    public function __construct()
    {
        parent::__construct('forms/changepassword');
        $this->setAttrib('id', 'change-password-form');
        $this->setAction($this->request->getBaseUrl() . '/' . $this->language . '/user/changepass');

        $auth = Zend_Auth::getInstance();
        $users = new Users();
        $user = $users->fetchRow('id = ' . $auth->getIdentity()->id);

        $oldPassword = $this->createElement('password', 'oldPassword');
        $oldPassword->setLabel($this->t->_('old_password') . ' :')
            ->setRequired(true)
            ->addValidator('StringLength', false, array(3, 50))
            ->addValidator('Identical', false, array($user['password']))
            ->setValue('');

        $password = $this->createElement('password', 'password');
        $password->setLabel($this->t->_('password') . ' :')
            ->setRequired(true)
            ->addValidator('StringLength', false, array(3, 50))
            ->addValidator('Identical', false, array($this->request->getPost('confirmPassword')))
            ->setValue('');
        $confirmPassword = $this->createElement('password', 'confirmPassword');
        $confirmPassword->setLabel($this->t->_('confirm_password') . ' :')
            ->setRequired(true)
            ->addValidator('StringLength', false, array(3, 50))
            ->addValidator('Identical', false, array($this->request->getPost('password')))
            ->setValue('');
        $val = $confirmPassword->getValidator('Identical');
        $val->setMessage($this->t->_('not_same'), Zend_Validate_Identical::NOT_SAME);

        $this->addElements(
            array(
                $oldPassword,
                $password,
                $confirmPassword,
            )
        );
    }

    public function validateAndSave($formData)
    {
        $formData['oldPassword'] = md5($formData['oldPassword']);
        if ($this->isValid($formData)) {
            $users = new Users();
            $users->update(
                array(
                    'password' => md5($formData['password'])
                ),
                'id = ' . Zend_Auth::getInstance()->getIdentity()->id
            );
            return true;
        }
        return false;
    }

}