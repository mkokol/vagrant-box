<?php
/** old one */

class Helpers_Forms_Decorator_FormRadio extends Zend_Form_Decorator_Abstract
{

    public function __construct($options = null) {
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
        $label = $element->getLabel();
        $name = $element->getName();
        $value = $element->getValue();
        $type = ($element->getType() == 'Zend_Form_Element_Radio') ? 'radio' : '';
        $option = $element->getMultiOptions();
        $listsep = $element->getSeparator();
        $itemList = array();
        foreach ($option as $opt_value => $opt_label){
            $checked = '';
            if ($opt_value == $value) {
                $checked = ' checked="checked"';
            }

            $optId = $element->getId() . '-' . $opt_value;

            $itemList[] =
                '<input type="' . $type . '" name="' . $name . '" id="' . $optId . '" value="' . $opt_value . '"' . $checked .'/>' .
                '<label for="' . $optId . '">' . $opt_label . '</label>';
        }
        $xhtmlElements = implode($listsep, $itemList);
        $labelClass = ($this->getOption('labelClass') != null ) ? ' class="'.$this->getOption('labelClass').' required"' : ' class="required"';
        $elementClass = ($this->getOption('elementClass') != null ) ? ' class="'.$this->getOption('elementClass').' box-form-element"' : ' class="box-form-element"';

        $errorsHtml = '';
        $errors = $element->getMessages();
        if(count($errors)){
            $errorsHtml .= '<ul class="errors">';
            foreach($errors as $error){
                $errorsHtml .= '<li>' . $error . '</li>';
            }
            $errorsHtml .= '</ul>';
        }

        $xhtml ="<dt $labelClass>{$label}</dt>
             <dd $elementClass>{$xhtmlElements}{$errorsHtml}</dd>";

        return $xhtml;
    }

}
