<?php
namespace AcceptanceHelpers;

class UserAcceptanceHelper extends AbstractAcceptanceHelper
{
    public function loginAsClient()
    {
        $this->I->click(['link' => 'Вход']);
        $this->I->waitForElement('#mbox-wnd', 30);
        $this->I->waitForJS('return mboxInProgres == false;', 30);
        $this->I->fillField('login_email', static::$userName . '@gmail.com');
        $this->I->fillField('login_password', static::$userName);
        $this->I->click('#login-btn');
        $this->I->waitForJS('return $.active == 0;', 30);
        $this->I->see('Выйти','a');
    }
}
