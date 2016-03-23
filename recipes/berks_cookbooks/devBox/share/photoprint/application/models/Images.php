<?php

class Images extends Model
{

    public static $_tableName = 'images';
    public static $noImgPath = '/public/theme/produces/none.png';

    public $quality = 90;
    public $extension = null;

    const IMG_DIR = 'images/';
    const IMG_ORIGIN_DIR = 'images/origin/';
    const IMG_TMP_DIR = 'images/tmp/';
    const IMG_AVATAR_DIR = 'images/img_avatar/';
    const IMG_AVATAR_TMP_DIR = 'images/tmp_avatar/';

    public static function getImagePath($baseUrl, $imgName, $size, $extension, $origin = false)
    {
        if ($imgName) {
            $imgNameParts = explode('-', $imgName);
            $folder = ($origin) ? self::IMG_ORIGIN_DIR : self::IMG_DIR;
//            if ($baseUrl == '/p') {
//                return 'http://photoprint.in.ua' . '/' . $folder . $imgNameParts[0][0] . '/' . $imgNameParts[1][0] . '/' . $imgName . '.' . $size . '.' . $extension;
//            }
            return $baseUrl . '/' . $folder . $imgNameParts[0][0] . '/' . $imgNameParts[1][0] . '/' . $imgName . '.' . $size . '.' . $extension;
        }
        return '';
    }

    public static function getAvatarPath($baseUrl, $imgName, $size, $extension)
    {
        if ($imgName) {
            $imgNameParts = explode('-', $imgName);
            return $baseUrl . '/' . self::IMG_AVATAR_DIR . $imgNameParts[0][0] . '/' . $imgNameParts[1][0] . '/' . $imgName . '.' . $size . '.' . $extension;
        }
        return '';
    }

    public static function getTmpImagePath($imgName, $degree, $extension)
    {
        $imgNameParts = explode('-', $imgName);
        return self::IMG_TMP_DIR . $imgNameParts[0][0] . '/' . $imgNameParts[1][0] . '/' . $imgName . '.' . $degree . '.' . $extension;
    }

    public function getExistImageId($imageName)
    {
        $oldImages = $this->fetchAll("name = '$imageName'")->toArray();
        return (count($oldImages)) ? $oldImages[0]['id'] : null;
    }

    public function insertNewImage($data)
    {
        $oldImages = $this->fetchAll('name = \'' . $data['name'] . '\'')->toArray();
        if (!count($oldImages)) {
            $data['created'] = date('Y-m-d H:i:s');
            return $this->insert($data);
        }
        return $oldImages[0]['id'];
    }

    public function createImageName($image)
    {
        if (!@class_exists('Imagick')) {
            ob_start();
            imagejpeg($image, NULL, $this->quality);
            $imageData = ob_get_contents();
            ob_end_clean();
            $imageDataMD5 = md5($imageData);
            $imageDataSHA1 = sha1($imageData);
            return $imageDataMD5 . '-' . $imageDataSHA1;
        } else {
            $imageData = $image->getImageBlob();
            $imageDataMD5 = md5($imageData);
            $imageDataSHA1 = sha1($imageData);
            return $imageDataMD5 . '-' . $imageDataSHA1;
        }
    }

    public function readImage($imgPathName, $imgTmpPath = '')
    {
        $image = null;
        $file_info = pathinfo($imgPathName);
        $extension = strtolower($file_info['extension']);
        if (($extension == 'jpg') || ($extension == 'jpeg')) {
            $this->extension = 'jpg';
        } else if ($extension == 'gif') {
            $this->extension = 'gif';
        } else if ($extension == 'png') {
            $this->extension = 'png';
        }
        if (!@class_exists('Imagick')) {
            $imgPath = ($imgTmpPath != '') ? $imgTmpPath : $imgPathName;
            if (($extension == 'jpg') || ($extension == 'jpeg')) {
                $image = imagecreatefromjpeg($imgPath);
            } else if ($extension == 'gif') {
                $image = imagecreatefromgif($imgPath);
            } else if ($extension == 'png') {
                $image = imagecreatefrompng($imgPath);
            }
        } else {
            $imgPath = ($imgTmpPath != '') ? $imgTmpPath : $imgPathName;
            $image = new Imagick();
            $image->readImage($imgPath);

        }
        return $image;
    }

    public function getImageWidth($image)
    {
        if (!@class_exists('Imagick')) {
            return imagesx($image);
        } else {
            return $image->getImageWidth();
        }
    }

    public function getImageHeight($image)
    {
        if (!@class_exists('Imagick')) {
            return imagesy($image);
        } else {
            return $image->getImageHeight();
        }
    }

    public function createNewImg($width, $height)
    {
        if (!@class_exists('Imagick')) {
            $newImage = ImageCreateTrueColor($width, $height);
            $imgAlloc = ImageColorAllocate($newImage, 255, 255, 255);
            ImageFill($newImage, 0, 0, $imgAlloc);
            ImageColorTransparent($newImage, $imgAlloc);
            ImageSaveAlpha($newImage, true);
        } else {
            $newImage = new Imagick();
            $newImage->newImage($width, $height, new ImagickPixel('transparent'));
        }
        return $newImage;
    }

    public function resize($image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        if (!@class_exists('Imagick')) {
            $newImage = $this->createNewImg($dst_w, $dst_h);
            imagecopyresized($newImage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        } else {
            $newImage = $this->createNewImg($dst_w, $dst_h);
            $newImage->readImageBlob($image->getImageBlob());
            $newImage->cropImage($src_w, $src_h, $src_x, $src_y);
            $newImage->resizeImage($dst_w, $dst_h, Imagick::FILTER_LANCZOS, 1, true);
        }
        return $newImage;
    }

    public function rotateImage($image, $degree)
    {
        if (!@class_exists('Imagick')) {
            return imagerotate($image, $degree, 0);
        } else {
            $image->rotateImage(new ImagickPixel('none'), $degree);
            return $image;
        }
    }

    public function saveImage($image, $name, $addParam, $dir)
    {
        $nameSections = explode('-', $name);
        $imageDataMD5 = $nameSections[0];
        if (!is_dir($dir . $imageDataMD5[0])) {
            mkdir($dir . $imageDataMD5[0]);
        }
        $imageDataSHA1 = $nameSections[1];
        if (!is_dir($dir . $imageDataMD5[0] . '/' . $imageDataSHA1[0])) {
            mkdir($dir . $imageDataMD5[0] . '/' . $imageDataSHA1[0]);
        }
        $path = $dir . $imageDataMD5[0] . '/' . $imageDataSHA1[0] . '/' . $name . '.' . $addParam . '.' . $this->extension;
        if (!@class_exists('Imagick')) {
            imagejpeg($image, $path);
        } else {
            $image->setImageFormat($this->extension);
            $image->setImageCompression(\Imagick::COMPRESSION_UNDEFINED);
            $image->setImageCompressionQuality(75);
            $image->writeImage($path);
        }
        return $path;
    }


    public static function compressImage($localImageFile)
    {
        $size = filesize($localImageFile);
        $type = @exif_imagetype($localImageFile);

        if ($type == IMAGETYPE_JPEG) {
            $oldSize = filesize($localImageFile);
            $img = imagecreatefromjpeg($localImageFile);
            if ($img) {
                $tmpfname = tempnam(sys_get_temp_dir(), 'phpJpg');
                imagejpeg($img, $tmpfname, 75); // 75% quality is good enough
                $size = filesize($tmpfname);
                imagedestroy($img);
                if ($size < $oldSize) {
                    // don't update file if size increased
                    rename($tmpfname, $localImageFile);
                }
            }
        } elseif ($type == IMAGETYPE_PNG) {
//            $img = @imagecreatefrompng($localImageFile);
//            if ($img) {
//                imagealphablending($img, true);
//                imagesavealpha($img, true);
//                imagepng($img, $localImageFile, 9);
//                imagedestroy($img);
//                $size = filesize($localImageFile);
//            }

            // using libs from http://pngquant.org/
            $oldSize = filesize($localImageFile);
            $tmpfname = tempnam(sys_get_temp_dir(), 'phpPng');
            $content = self::compress_png($localImageFile);
            file_put_contents($tmpfname, $content);
            $size = filesize($tmpfname);
            if ($size < $oldSize) {
                // don't update file if size increased
                rename($tmpfname, $localImageFile);
            }
        } else {
            return "00000000";
        }
        return $size;
    }

    private static function compress_png($path_to_png_file, $max_quality = 90)
    {
        if (!file_exists($path_to_png_file)) {
            throw new \Exception("File does not exist: $path_to_png_file");
        }
        // guarantee that quality won't be worse than that.
        $min_quality = 60;
        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg( $path_to_png_file));

        if (!$compressed_png_content) {
            throw new \Exception("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }
        return $compressed_png_content;
    }
}
