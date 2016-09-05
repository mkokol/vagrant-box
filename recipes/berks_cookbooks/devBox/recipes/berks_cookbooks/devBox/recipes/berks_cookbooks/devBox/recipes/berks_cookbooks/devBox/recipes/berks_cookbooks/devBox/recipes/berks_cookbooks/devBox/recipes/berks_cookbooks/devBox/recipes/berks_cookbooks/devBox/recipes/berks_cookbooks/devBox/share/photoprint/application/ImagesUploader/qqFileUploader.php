<?php
/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class ImagesUploader_qqFileUploader
{
    private $allowedExtensions = array();
    private $sizeLimit = 1048576; // 1 * 1024 * 1024
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = null)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
        $this->allowedExtensions = $allowedExtensions;
        if ($sizeLimit) {
            $this->sizeLimit = $sizeLimit;
        }
        $this->checkServerSettings();
        if (isset($_GET['qqfile'])) {
            $this->file = new ImagesUploader_qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new ImagesUploader_qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload()
    {
        if (!$this->file) {
            return array('error' => 'No files were uploaded.');
        }
        $size = $this->file->getSize();
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        $pathinfo = pathinfo($this->file->getName());
        $ext = $pathinfo['extension'];
        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
        }

        $folder = rand(0, 32);
        $uploadDirectory = Images::IMG_AVATAR_TMP_DIR . $folder . '/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, '0755');
        }

        $filename = rand(100000, 999999);
        while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
            $filename = rand(100000, 999999);
        }
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
            $imgInfo = array(
                'imgPath' => "$uploadDirectory$filename.$ext",
                'imgName' => "$filename.$ext",
                'folder' => $folder,
                'success' => true
            );
            return $imgInfo;
        } else {
            return array(
                'error' => 'Could not save uploaded file. The upload was cancelled, or server error encountered'
            );
        }
    }
}