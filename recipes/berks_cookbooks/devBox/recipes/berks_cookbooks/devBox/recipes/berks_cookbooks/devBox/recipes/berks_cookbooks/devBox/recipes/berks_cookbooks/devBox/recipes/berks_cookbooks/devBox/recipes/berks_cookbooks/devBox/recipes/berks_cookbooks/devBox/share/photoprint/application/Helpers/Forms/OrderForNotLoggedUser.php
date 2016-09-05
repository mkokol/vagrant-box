<?php

class Helpers_Forms_OrderForNotLoggedUser extends Helpers_Forms_OrderForLoggedUser
{
    public function __construct($selectedItems)
    {
        parent::__construct($selectedItems);

        $password = $this->createElement('password', 'password');
        $password->setLabel($this->t->_('password') . ' :')
            ->addValidator('StringLength', false, [3, 50])
            ->addValidator('Identical', false, [$this->request->getPost('confirm_password')])
            ->setRequired(true)
            ->setValue('')
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormPassword');

        $confirmPassword = $this->createElement('password', 'confirm_password');
        $confirmPassword->setLabel($this->t->_('confirm_password') . ' :')
            ->addValidator('StringLength', false, [3, 50])
            ->addValidator('Identical', false, [$this->request->getPost('password')])
            ->setRequired(true)
            ->setValue('')
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormPassword');
        $val = $confirmPassword->getValidator('Identical');
        $val->setMessage($this->t->_('not_same'), Zend_Validate_Identical::NOT_SAME);

        $this->addElements([
            $password,
            $confirmPassword
        ]);
    }

    public function save($selectedItems, $refKey)
    {
        $userIdBeforeSignUp = Users::getCarrentUserId();
        $createdAt = date('Y-m-d H:i:s');
        $users = new Users();
        $users->insert(
            [
                'user_name' => $this->getValue('user_name'),
                'email'     => $this->getValue('email'),
                'phone'     => $this->getValue('phone'),
                'password'  => md5($this->getValue('password')),
                'created'   => $createdAt
            ]
        );
        Users::login(
            $this->getValue('email'),
            $this->getValue('password')
        );

        $baskets = new Baskets();
        $baskets->update(
            ['user_id' => Users::getCarrentUserId()],
            'user_id = \'' . $userIdBeforeSignUp . '\''
        );

        return $this->saveOrder($selectedItems, $refKey);
    }
}
