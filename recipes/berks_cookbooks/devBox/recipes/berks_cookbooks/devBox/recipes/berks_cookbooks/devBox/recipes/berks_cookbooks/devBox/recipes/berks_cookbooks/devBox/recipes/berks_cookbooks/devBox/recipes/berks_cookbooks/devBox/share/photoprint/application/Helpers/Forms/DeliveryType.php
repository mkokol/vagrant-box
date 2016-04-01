<?php

/**
 * TODO: Delete it.
 *
 * Class Helpers_Forms_DeliveryType
 */
class Helpers_Forms_DeliveryType extends Helpers_Forms_GenForm
{
    private $session;

    public function __construct($session)
    {
        parent::__construct('forms/delivery_type');
        $this->session = $session;
        $base_url = $this->request->getBaseUrl();
        // add main form config
        $this->setName('payment');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/user/create-order?items=' . $this->session->itemSelected);

        $current_form = new Zend_Form_Element_Hidden('currentForm');
        $current_form->setValue('3');
        $current_form->setLabel('3');

        $userName = $this->createElement('text', 'user_name');
        $userName->addValidator('NotEmpty', false)
            ->setRequired(true)
            ->setLabel($this->t->_('user_name') . ' :');

        $phone = $this->createElement('text', 'phone');
        $phone->setLabel($this->t->_('phone') . ' :')
            ->setRequired(true)
            ->addValidator('regex', false, ['/^[+]{0,1}+[0-9|(|)]*$/']);
        $val = $phone->getValidator('regex');
        $val->setMessage($this->t->_('not_match'), Zend_Validate_Regex::NOT_MATCH);

        $email = $this->createElement('text', 'email');
        $email->setLabel($this->t->_('email') . ' :')
            ->addFilter('StringToLower')
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress')
            ->addValidator(new Helpers_Validator_UniqueEmail());

        $address = $this->createElement('textarea', 'address');
        $address->addValidator('NotEmpty', false)
            ->setAttrib('cols', '35')
            ->setAttrib('rows', '7')
            ->setLabel($this->t->_('address') . ' :');

        $this->addElements([$current_form, $userName, $phone, $email, $address]);

        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                foreach ($element->getDecorators() as $decorator) {
                    $decorator->setOption('class', 'hidden');
                }
            }
        }
    }

    public function validateAndSave($formData)
    {
        if ($this->isValid($formData)) {
            $sum = 0;
            $orderedItems = [];
            $baskets = new Baskets();
            $item_el = '';
            $el = explode(',', $this->session->itemSelected);

            foreach ($el as $value) {
                $item_el .= ($item_el == '') ? ' id = ' . $value : ' OR id = ' . $value;
            }

            $otherElCount = $baskets->fetchAll($item_el)->toArray();
            Helpers_General_ControllerAction::staticLoadTranslation('products');
            $t = Helpers_General_ControllerAction::getLoadedTranslation();

            foreach ($otherElCount as $value) {
                $xmlData = new SimpleXMLElement(stripslashes($value['dataXml']));
                $price = ProductsItems::getProductItemPrice($value);
                $sum += $price * $value['count'];
                $orderedItems[] = [
                    'id'    => $value['id'],
                    'name'  => $t->_((string)$xmlData->item),
                    'count' => $value['count'],
                    'price' => $price
                ];
                $baskets->update(
                    ['payment' => $price * $value['count']],
                    'id = ' . $value['id']
                );
            }

            $orders = new Orders();
            $orderId = $orders->insert([
                'user_id'           => Users::getCarrentUserId(),
                'user_name'         => $formData['user_name'],
                'phone'             => $formData['phone'],
                'address'           => $formData['address'],
                'shop_code'         => $formData['shopCode'],
                'status'            => 'created',
                'date'              => date('Y-m-d H:i:s'),
                'dostavka_else'     => $this->session->transport_tovar,
                'dostavka_lustivok' => '',
                'payment_sys'       => $this->session->paymentType,
                'payment'           => $sum
            ]);
            $baskets->update(
                ['status' => 'inOrder', 'order_id' => $orderId],
                $item_el
            );
            $this->sendEmails();

            return [
                'status'       => 'done',
                'orderId'      => $orderId,
                'sum'          => $sum,
                'orderedItems' => $orderedItems
            ];
        }

        return ['status' => 'faile'];
    }

    public function sendEmails()
    {
        try {
            $mail = new Helpers_General_Mail();
            $mail->addTo('vovychk@gmail.com', 'Kokolius Volodymyr');
            $mail->setSubject('photoprint.in.ua - Замовлення');
            $body = sprintf('Нове замовлення на http://photoprint.in.ua/ua/admin/orders');
            $mail->setBodyHtml($body);
            $mail->send();

            $mail = new Helpers_General_Mail();
            $mail->addTo('mickokolius@gmail.com', 'Kokolius Mykhailo');
            $mail->setSubject('photoprint.in.ua - Замовлення');
            $body = sprintf('Нове замовлення на http://photoprint.in.ua/ua/admin/orders');
            $mail->setBodyHtml($body);
            $mail->send();
        } catch (Exception $e) {
        }
    }
}
