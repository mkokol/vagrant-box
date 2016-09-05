<?php

/** old one */
class Helpers_Forms_Decorator_FormRadioPayment extends Zend_Form_Decorator_Abstract
{

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * Render a MultiCheckbox element with custom layer
     *
     * Replaces $content entirely from currently set element.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $name = $element->getName();
        $value = $element->getValue();
        $type = ($element->getType() == 'Zend_Form_Element_Radio') ? 'radio' : '';
        $option = $element->getMultiOptions();
        $itemList = '';

        foreach ($option as $opt_value => $opt_label) {
            $checked = '';

            if ($opt_value == $value) {
                $checked = ' checked="checked"';
            }

            $optId = $element->getId() . '-' . $opt_value;
            $itemList .=
                '<label class="full-width" for="' . $optId . '">'
                    . '<input type="' . $type . '" name="' . $name . '" '
                        . 'id="' . $optId . '" value="' . $opt_value . '"' . $checked . '/>'
                    . ' ' . $opt_label
                . '</label>';
        }

        $errors = $element->getMessages();
        $errorBlock = '';

        if (count($errors)) {
            foreach ($errors as $error) {
                $errorBlock .= '<span class="error">' . $error . '</span>';
            }
        }

        return $errorBlock . $itemList;
    }

}
