<?php

class Helpers_Forms_OrderForLoggedUser extends Helpers_Forms_GenForm
{
    public function __construct($selectedItems)
    {
        parent::__construct('forms/order');
        $base_url = $this->request->getBaseUrl();
        // add main form config
        $this->setName('order');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/user/create-order');

        $userName = $this->createElement('text', 'user_name');
        $userName->addValidator('NotEmpty', false)
            ->setRequired(true)
            ->setLabel($this->t->_('user_name') . ' :')
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormInput');

        $phone = $this->createElement('text', 'phone');
        $phone->setLabel($this->t->_('phone') . ' :')
            ->setRequired(true)
            ->addValidator('regex', false, ['/^[+]{0,1}+[0-9|(|)]*$/'])
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormInput');
        $validator = $phone->getValidator('regex');
        $validator->setMessage($this->t->_('not_match'), Zend_Validate_Regex::NOT_MATCH);

        $email = $this->createElement('text', 'email');
        $email->setLabel($this->t->_('email') . ' :')
            ->addFilter('StringToLower')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress')
            ->addValidator(new Helpers_Validator_UniqueEmail())
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormInput');

        $payment = $this->createElement('radio', 'payment');
        $payment->setRequired(true)
            ->setLabel($this->t->_('payment_sys'))
            ->setMultiOptions([
                'privatbank'   => $this->t->_('privatbank'),
                'webmoney'     => $this->t->_('web_money'),
                'afterreceive' => $this->t->_('after_receive')
            ])
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormRadioPayment');

        $baskets = new Baskets();
        $item_el = '';
        $el = explode(',', $selectedItems);

        foreach ($el as $value) {
            $item_el .= ($item_el == '') ? ' id = ' . $value : ' OR id = ' . $value;
        }

        $otherElCount = $baskets->fetchAll("product_group != 'postcard' AND (" . $item_el . ")")->count();
        $vidkrutkaCount = $baskets->fetchAll("product_group = 'postcard' AND (" . $item_el . ")")->count();
        $items = [];

        if ($otherElCount > 0) {
            $items['NewPost'] = $this->t->_('new_post');
            $items['AvtoLyks'] = $this->t->_('avto_lyks');
        } elseif ($vidkrutkaCount > 0) {
            $items['letter'] = $this->t->_('letter');
            $items['specialLetter'] = $this->t->_('specialLetter');
        }

        $delivery = $this->createElement('radio', 'delivery');
        $delivery->setRequired(true)
            ->setLabel($this->t->_('how_to_send_tovar'))
            ->setMultiOptions($items)
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormRadioDelivery');

        $address = $this->createElement('textarea', 'address');
        $address->addValidator('NotEmpty', false)
            ->setAttrib('cols', '50')
            ->setAttrib('rows', '5')
            ->setLabel($this->t->_('address') . ' :')
            ->addPrefixPath(
                'Helpers_Forms_Decorator',
                'Helpers/Forms/Decorator/',
                'decorator'
            )
            ->addDecorator('FormTextarea');

        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $userDate = $auth->getIdentity();
            $userName->setValue($userDate->user_name);
            $email->setValue($userDate->email);
            $phone->setValue($userDate->phone);

            $orders = new Orders();
            $lastOrder = $orders->getUsersLastOrder($userDate->id);

            if ($lastOrder) {
                $payment->setValue($lastOrder->payment_sys);
                $delivery->setValue($lastOrder->dostavka_else);
                $address->setValue($lastOrder->address);
            }
        }

        $this->addElements([
            $userName,
            $email,
            $phone,
            $payment,
            $delivery,
            $address
        ]);
    }

    public function save($selectedItems, $refKey)
    {
        return $this->saveOrder($selectedItems, $refKey);
    }

    protected function saveOrder($selectedItems, $refKey)
    {
        $sum = 0;
        $baskets = new Baskets();
        $otherElCount = $baskets
            ->fetchAll('id IN (' . $selectedItems . ')')
            ->toArray();

        foreach ($otherElCount as $value) {
            $price = ProductsItems::getProductItemPrice($value);
            $sum += $price * $value['count'];
            $baskets->update(
                ['payment' => $price * $value['count']],
                'id = ' . $value['id']
            );
        }

        $orders = new Orders();
        $orderId = $orders->insert([
            'user_id'           => Users::getCarrentUserId(),
            'language'          => $this->language,
            'user_name'         => $this->getValue('user_name'),
            'phone'             => $this->getValue('phone'),
            'address'           => $this->getValue('address'),
            'shop_code'         => $refKey,
            'status'            => 'created',
            'date'              => date('Y-m-d H:i:s'),
            'dostavka_else'     => $this->getValue('delivery'),
            'dostavka_lustivok' => '',
            'payment_sys'       => $this->getValue('payment'),
            'payment'           => $sum
        ]);
        $baskets->update(
            [
                'status'   => 'inOrder',
                'order_id' => $orderId
            ],
            'id IN (' . $selectedItems . ')'
        );

        return $orderId;
    }
}
