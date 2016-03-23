<?php

/**
 * general settings for all forms
 */
class Helpers_Forms_GenForm extends Zend_Form
{
    protected $request;
    protected $language;
    /** @var $t Helpers_General_Translate */
    protected $t;

    public function __construct($translationPath)
    {
        parent::__construct(null);

        Helpers_General_ControllerAction::staticLoadTranslation($translationPath);
        $this->request = Zend_Controller_Front::getInstance()->getRequest();
        $this->language = (Helpers_General_Translate::getLanguage() == 'uk') ? 'ua' : 'ru';
        $this->t = Helpers_General_ControllerAction::getLoadedTranslation();
        $this->setTranslator(
            Helpers_General_Translate::getValidationMessages()
        );
    }

    public function addCustomDecorator()
    {
        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                foreach ($element->getDecorators() as $decorator) {
                    $decorator->setOption('class', 'hidden');
                }
            }
        }
    }
}
