<?php

class Helpers_General_Translate extends Zend_Translate
{

    private static $language;

    /**
     * @param array|Zend_Config $filePath
     * @param $language
     */
    public function __construct($filePath, $language)
    {
        self::$language = ($language == 'ua') ? 'uk' : $language;

        parent::__construct([
            'adapter' => 'tmx',
            'content' => 'application/languages/' . $filePath . '.xml',
            'locale'  => self::$language
        ]);
    }

    /**
     * @param string $filePath
     */
    public function addTranslation($filePath, $language = null)
    {
        parent::addTranslation([
            'adapter' => 'tmx',
            'content' => "application/languages/$filePath.xml",
            'locale'  => ($language !== null) ? $language : self::$language
        ]);
    }

    public static function getLanguage()
    {
        return self::$language;
    }

    public static function getValidationMessages(){
        $translation = new Zend_Translate([
            'adapter' => 'tmx',
            'content' => 'application/languages/forms/validation.xml',
            'locale'  => self::$language
        ]);

        $data = [
            Zend_Captcha_Image::BAD_CAPTCHA                => $translation->_('bad_captcha'),
            Zend_Validate_NotEmpty::IS_EMPTY               => $translation->_('is_empty'),
            Zend_Validate_StringLength::TOO_SHORT          => $translation->_('too_short'),
            Zend_Validate_StringLength::TOO_LONG           => $translation->_('too_long'),
            Zend_Validate_EmailAddress::INVALID            => $translation->_('invalid'),
            Zend_Validate_EmailAddress::INVALID_HOSTNAME   => $translation->_('invalid_hostname'),
            Zend_Validate_EmailAddress::INVALID_MX_RECORD  => $translation->_('invalid_mx_record'),
            Zend_Validate_EmailAddress::DOT_ATOM           => $translation->_('dot_atom'),
            Zend_Validate_EmailAddress::QUOTED_STRING      => $translation->_('quoted_string'),
            Zend_Validate_EmailAddress::INVALID_LOCAL_PART => $translation->_('invalid_local_part'),
            Zend_Validate_Identical::MISSING_TOKEN         => $translation->_('missing_token'),
            Zend_Validate_Identical::NOT_SAME              => $translation->_('not_match'),
            Helpers_Validator_UniqueEmail::NOT_UNIQUE      => $translation->_('not_unique_email'),
            Helpers_Validator_UniqueNickname::NOT_UNIQUE   => $translation->_('not_unique_login')
        ];

        return new Zend_Translate_Adapter_Array($data, self::$language);
    }
}
