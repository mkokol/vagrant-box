<?php
namespace AcceptanceHelpers;

use AcceptanceTester;

class CoreAcceptanceHelper extends AcceptanceTester
{
    private $helpers = [];

    public function getUserHelper()
    {
        if (!isset($this->helpers['user'])) {
            $this->helpers['user'] = new UserAcceptanceHelper($this);
        }
        return $this->helpers['user'];
    }

    public function getRegistrationHelper()
    {
        if (!isset($this->helpers['registration'])) {
            $this->helpers['registration'] = new RegistrationAcceptanceHelper($this);
        }
        return $this->helpers['registration'];
    }
}
