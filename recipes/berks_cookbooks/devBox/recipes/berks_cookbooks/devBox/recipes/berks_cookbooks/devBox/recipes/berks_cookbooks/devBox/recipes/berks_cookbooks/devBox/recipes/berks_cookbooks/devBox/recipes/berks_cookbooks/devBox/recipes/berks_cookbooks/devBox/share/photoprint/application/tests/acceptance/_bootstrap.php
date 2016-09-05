<?php

Codeception\Util\Autoload::register(
    'AcceptanceHelpers',
    'AcceptanceHelper',
    dirname( dirname( __FILE__ ) ) . '/lib/tests/AcceptanceHelpers'
);
