<?php

class Helpers_View_Seo extends Helpers_View_H
{
    public $transliteration = [
        'ua' => [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ґ' => 'g',
            'д' => 'd', 'е' => 'e', 'є' => 'ye', 'ж' => 'zh', 'з' => 'z',
            'и' => 'y', 'і' => 'i', 'ї' => 'yi', 'й' => 'j', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
            'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
            'х' => 'x', 'ц' => 'cz', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh',
            'ь' => '', 'ю' => 'yu', 'я' => 'ya', ' ' => '-', '.' => '',
            ',' => '', ':' => '', ';' => '', '!' => '', '?' => '',
            '\'' => '', '"' => ''
        ],
        'ru' => [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'yi', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya', ' ' => '-', '.' => '',
            ',' => '', ':' => '', ';' => '', 'є' => 'e', '!' => '',
            '?' => '', '\'' => '', '"' => ''
        ]
    ];

    public function text($text)
    {
        return str_replace('{{BASE_URL}}', $this->baseUrl, $text);
    }

    public function proposeCategoryUrl($lang, $item, $name)
    {
        if ($this->t == null) {
            $this->t = $view = Zend_Layout::startMvc()->getView()->t;
        }

        $name = mb_strtolower($name, "UTF-8");
        $newName = '';

        foreach ($this->mbStringToArray($name) as $letter) {
            $newName .= isset($this->transliteration[$lang][$letter])
                ? $this->transliteration[$lang][$letter]
                : $letter;
        }

        return '/' . $lang . '/' . $this->t->_($item . '_seo_url_prefix') . '-' . $newName . '-' . $this->t->_($item . '_seo_url_ending');
    }

    function mbStringToArray($string)
    {
        $strLen = mb_strlen($string);
        $array = [];

        while ($strLen) {
            $array[] = mb_substr($string, 0, 1, "UTF-8");
            $string = mb_substr($string, 1, $strLen, "UTF-8");
            $strLen = mb_strlen($string);
        }

        return $array;
    }
}
