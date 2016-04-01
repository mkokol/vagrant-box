<?php

class CronController extends Helpers_General_ControllerAction
{
    public function sendEmailAction()
    {

    }

    public function reindexSearchAction()
    {
        $searchContent = new SearchContent();
        $searchContent->reindex();

        exit;
    }
}
