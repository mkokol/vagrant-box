<?php

class Helpers_Forms_ResetPassword extends Helpers_Forms_GenForm
{

    public function __construct()
    {
        parent::__construct('forms/resetpassword');

        $password = $this->createElement('password', 'password');
        $password->setLabel($this->t->_('password') . ' :')
            ->addValidator('Identical', false, [$this->request->getPost('confirmPassword')])
            ->setRequired(true)
            ->setValue('');
        $confirmPassword = $this->createElement('password', 'confirmPassword');
        $confirmPassword->setLabel($this->t->_('confirm_password') . ' :')
            ->addValidator('Identical', false, [$this->request->getPost('password')])
            ->setRequired(true)
            ->setValue('');
        $val = $confirmPassword->getValidator('Identical');
        $val->setMessage($this->t->_('not_same'), Zend_Validate_Identical::NOT_SAME);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->t->_('save_btn'));
        $submit->setAttrib('class', 'button');

        $this->addElements([$password, $confirmPassword, $submit]);

        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                foreach ($element->getDecorators() as $decorator) {
                    $decorator->setOption('class', 'hidden');
                }
            }
        }
    }
}