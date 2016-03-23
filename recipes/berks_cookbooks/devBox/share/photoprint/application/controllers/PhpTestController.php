<?php

class PhpTestController extends Helpers_General_ControllerAction
{
    public function uploadParamsAction()
    {
        $this->removeDefaultView();
        $this->viewJson([
            'status'              => 'success',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size'       => ini_get('post_max_size')
        ]);
    }
}
