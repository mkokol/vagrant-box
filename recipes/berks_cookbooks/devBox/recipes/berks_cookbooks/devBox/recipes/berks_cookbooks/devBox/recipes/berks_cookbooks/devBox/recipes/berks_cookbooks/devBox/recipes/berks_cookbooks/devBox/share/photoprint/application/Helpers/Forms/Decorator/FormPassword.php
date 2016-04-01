<?php

class Helpers_Forms_Decorator_FormPassword extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();


        $name = htmlentities($element->getFullyQualifiedName());
        $label = htmlentities($element->getLabel());
        $id = htmlentities($element->getId());
        $value = htmlentities($element->getValue());

        $labelBlock = sprintf(
            '<label for="%s">%s</label>',
            $id,
            $label
        );
        $fieldBlock = sprintf(
            '<input id="%s" name="%s" type="password"/>',
            $id,
            $name
        );

        $errors = $element->getMessages();

        $errorBlock = '';
        if (count($errors)) {
            $fieldBlock = str_replace('"/>', '" class="error"/>', $fieldBlock);

            foreach ($errors as $error) {
                $errorBlock .= '<span class="error error-input">' . $error . '</span>';
            }
        }

        return $labelBlock . $fieldBlock . $errorBlock;
    }
}
