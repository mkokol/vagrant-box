<?php

class Helpers_Forms_Transportation extends Helpers_Forms_GenForm
{
    private $session;

    public function __construct($session)
    {
        parent::__construct('forms/transportation');
        $this->session = $session;
        $base_url = $this->request->getBaseUrl();
        // add main form config
        $this->setName('trensportation');
        $this->setMethod('post');
        $this->setAction($base_url . '/' . $this->language . '/user/create-order?items=' .$this->session->itemSelected);

        $current_form = new Zend_Form_Element_Hidden('currentForm');
        $current_form->setValue('2');
        $current_form->setLabel('2');
        $this->addElement($current_form);

        $baskets = new Baskets();
        $item_el = '';
        $el = explode(',', $this->session->itemSelected);
        foreach ($el as $value) {
            $item_el .= ($item_el == '') ? ' id = ' . $value : ' OR id = ' . $value;
        }
        $otherElCount = $baskets->fetchAll("product_group != 'postcard' AND (" . $item_el . ")")->count();
        $vidkrutkaCount = $baskets->fetchAll("product_group = 'postcard' AND (" . $item_el . ")")->count();

        $items = array();
        if ($otherElCount > 0) {
            $items['NewPost'] = ' ' . $this->t->_('new_post');
            $items['AvtoLyks'] = ' ' . $this->t->_('avto_lyks');
        } elseif ($vidkrutkaCount > 0) {
            $items['letter'] = ' ' . $this->t->_('letter');
            $items['specialLetter'] = ' ' . $this->t->_('specialLetter');
        }
        $tovars = new Zend_Form_Element_Radio('transportTovar');
        $tovars->setRequired(true)
            ->setLabel($this->t->_('how_to_send_tovar'))
            ->setMultiOptions($items);
        if (($this->request->getPost('currentForm') == '3') || ($this->request->getParam('type', '') == 'back')) {
            $tovars->setValue($this->session->transport_tovar);
        }
        $this->addElement($tovars);
        $this->addCustomDecorator();
    }

    public function isValid($data)
    {
        $isValid = parent::isValid($data);
        if($isValid){
            $this->session->transport_tovar = $data['transportTovar'];
        }
        return $isValid;
    }
}
