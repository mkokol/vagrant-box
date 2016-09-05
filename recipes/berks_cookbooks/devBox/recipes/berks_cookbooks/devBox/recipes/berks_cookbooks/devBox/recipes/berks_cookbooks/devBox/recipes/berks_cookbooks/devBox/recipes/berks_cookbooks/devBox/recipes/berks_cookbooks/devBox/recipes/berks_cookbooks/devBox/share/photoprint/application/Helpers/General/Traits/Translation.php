<?php

trait Helpers_General_Traits_Translation
{
    private static $translations = [];

    public function loadTranslation($filePath, $language = null, $group = 'default')
    {
        if (is_array($filePath)) {
            foreach ($filePath as $translationFile) {
                self::staticLoadTranslation($translationFile, $language, $group);
            }
        } else {
            self::staticLoadTranslation($filePath, $language, $group);
        }
    }

    public function appendLoadedTranslation()
    {
        $t = self::getLoadedTranslation();

        if ($t === null) {
            return;
        }

        $this->view->helper_title2 = $this->view->title;
        $this->view->helper_title3 = $t->_('title');
        $this->view->helper_description2 = $this->view->description;
        $this->view->helper_description3 = $t->_('description');
        $this->view->helper_keywords2 = $this->view->keywords;
        $this->view->helper_keywords3 = $t->_('keywords');

        $tags = ['title', 'description', 'keywords'];
        foreach ($tags as $tag) {
            if (!$this->view->$tag) {
                $this->view->$tag = ($t->_($tag) != $tag) ? $t->_($tag) : '';
            } else if (($t->_($tag) != $tag) && ($t->_($tag) != '')) {
                $this->view->$tag = $t->_($tag);
            }
        }

        $this->view->t = $t;
    }

    public static function staticLoadTranslation($filePath, $language = null, $group = 'default')
    {
        $defaultLanguage = ($language !== null) ? $language : Helpers_General_UrlManager::getLanguage();

        if (!isset(self::$translations[$group])) {
            self::$translations[$group] = new Helpers_General_Translate($filePath, $defaultLanguage);
        } else {
            self::$translations[$group]->addTranslation($filePath, $defaultLanguage);
        }
    }

    /**
     * @param string $group
     * @return Helpers_General_Translate
     */
    public static function getLoadedTranslation($group = 'default')
    {
        return self::$translations[$group];
    }
}
