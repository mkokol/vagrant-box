<?php

namespace AcceptanceHelpers;

class RegistrationAcceptanceHelper extends AbstractAcceptanceHelper
{
    public function registrationAsClient()
    {
        static::$userName = substr(md5(time()), 1, 9);

        $this->I->fillField('user_name', static::$userName);
        $this->I->fillField('email', static::$userName . '@gmail.com');
        $this->I->fillField('password', static::$userName);
        $this->I->fillField('confirmPassword', static::$userName);
        $this->I->fillField('phone', '123456789');
        $this->I->fillField('captcha[input]', 'NO_CAPTCHA-super_captcha_hack');
        $this->I->wait(AbstractAcceptanceHelper::SLEEP_BETWEEN_SECTION);

        $this->I->click('#submit');
        $this->I->see('Поздравляем! Pегистрация завеншена.');
    }
}
