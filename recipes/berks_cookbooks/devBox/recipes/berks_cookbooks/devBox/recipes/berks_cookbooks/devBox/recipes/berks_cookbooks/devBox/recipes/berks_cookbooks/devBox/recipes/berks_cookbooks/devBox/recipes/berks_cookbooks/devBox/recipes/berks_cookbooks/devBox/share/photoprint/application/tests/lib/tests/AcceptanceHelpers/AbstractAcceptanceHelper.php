<?php
namespace AcceptanceHelpers;

class AbstractAcceptanceHelper
{
    const SLEEP_BETWEEN_SECTION = 2;
    const SLEEP_BETWEEN_TESTS = 5;

    /** @var  string */
    protected static $userName;

    /** @var CoreAcceptanceHelper */
    protected $I;

    public function __construct($I)
    {
        $this->I = $I;
    }
}
