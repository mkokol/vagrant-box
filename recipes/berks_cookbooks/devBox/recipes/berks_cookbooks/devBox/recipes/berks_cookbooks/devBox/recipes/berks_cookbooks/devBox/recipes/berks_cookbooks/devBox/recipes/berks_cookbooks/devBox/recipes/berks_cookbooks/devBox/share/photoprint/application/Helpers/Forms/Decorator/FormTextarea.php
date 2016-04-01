<?php

class Helpers_Forms_Decorator_FormTextarea extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $element = $this->getElement();


        $name = htmlentities($element->getFullyQualifiedName());
        $label = htmlentities($element->getLabel());
        $id = htmlentities($element->getId());
        $value = htmlentities($element->getValue());
        $rows = $element->getAttrib('rows');
        $cols = $element->getAttrib('cols');

        $labelBlock = sprintf(
            '<label for="%s" class="full-width">%s</label>',
            $id,
            $label
        );
        $fieldBlock = sprintf(
            '<textarea id="%s" name="%s" rows="%s" cols="%s">%s</textarea>',
            $id,
            $name,
            $rows,
            $cols,
            $value
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
