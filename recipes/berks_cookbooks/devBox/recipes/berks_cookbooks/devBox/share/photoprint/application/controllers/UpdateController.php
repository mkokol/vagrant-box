<?php

/**
 * TODO: Delete this file!!!
 */
class UpdateController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function indexAction()
    {
        $language = Helpers_General_UrlManager::getLanguage();
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        echo '<html>';
        echo '<head>';
        echo '<script type="text/javascript" src="' . $this->_request->getBaseUrl() . '/public/scripts/lib/jquery.min.js?v=1.329"></script>';
        echo '<script type="text/javascript">';
        echo "upd = {
                md5list : ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'],
                sha1list : ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'],
                md5Id : 0,
                sha1Id : 0,
                update : function(){
                    $.ajax({
                        type: 'POST',
                        url: '" . $this->_request->getBaseUrl() . "/" . $language . "/update/img',
                        data: {
                            md5: upd.md5list[upd.md5Id],
                            sha1: upd.sha1list[upd.sha1Id]
                        },
                        success: function(data) {
                            console.log(data)
                            if(typeof upd.sha1list[upd.sha1Id + 1] == 'undefined'){
                                upd.sha1Id = 0;
                                if(typeof upd.md5list[upd.md5Id + 1] == 'undefined'){
                                    upd.md5Id = 0;
                                }else{
                                    upd.md5Id++;
                                    upd.update();
                                }
                                }else{
                                upd.sha1Id++;
                                upd.update();
                            }
                        }
                    });
                }
            };";
        echo '$(document).ready(function(){';
        echo '    upd.update();';
        echo '});';
        echo '</script>';
        echo '</head>';
        echo '<body>';
        echo 'do';
        echo '</body>';
        echo '</html>';
        die;
    }


    public function imgAction()
    {
        set_time_limit(0);
        $ids = array();
        $dir = Images::IMG_DIR . $this->_request->getParam('md5') . '/' . $this->_request->getParam('sha1');

        $images = new Images();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ((strpos($file, '.s.') !== false) || (strpos($file, '.p.') !== false)) {
//                        $imgName = str_replace('.o.jpg', '', $file);
//                        $imagePath = self::getImagePath('.', $imgName, 'o');
//                        $newImagePath = self::getNewImagePath('.', $imgName, 'o');
//                        if (copy($imagePath, $newImagePath)) {
//                            unlink($imagePath);
//                        }
                        $ids[] = ' - ' . $file . '[' .
                            $images->compressImage(Images::IMG_DIR . $this->_request->getParam('md5') . '/' . $this->_request->getParam('sha1') . '/' . $file)
                        . ']';
                    }
                }
                closedir($dh);
            }
        }
        echo json_encode(array('ids' => $ids));
        die;
    }


    public static function getImagePath($baseUrl, $imgName, $size)
    {
        if ($imgName) {
            $imgNameParts = explode('-', $imgName);
            return $baseUrl . '/' . Images::IMG_DIR . $imgNameParts[0][0] . '/' . $imgNameParts[1][0] . '/' . $imgName . '.' . $size . '.jpg';
        }
        return '';
    }

    public static function getNewImagePath($baseUrl, $imgName, $size)
    {
        if ($imgName) {
            $imgNameParts = explode('-', $imgName);


            $dir = Images::IMG_ORIGIN_DIR;
            $nameSections = explode('-', $imgName);
            $imageDataMD5 = $nameSections[0];
            if (!is_dir($dir . $imageDataMD5[0])) {
                mkdir($dir . $imageDataMD5[0]);
            }
            $imageDataSHA1 = $nameSections[1];
            if (!is_dir($dir . $imageDataMD5[0] . '/' . $imageDataSHA1[0])) {
                mkdir($dir . $imageDataMD5[0] . '/' . $imageDataSHA1[0]);
            }


            return $baseUrl . '/' . Images::IMG_ORIGIN_DIR . $imgNameParts[0][0] . '/' . $imgNameParts[1][0] . '/' . $imgName . '.' . $size . '.jpg';
        }
        return '';
    }

    public function testImg (){
        $base = new Imagick('U0R4F.png');
        $mask = new Imagick('mask.png');
        $over = new Imagick('3ulkM.png');

        // Setting same size for all images
        $base->resizeImage(274, 275, Imagick::FILTER_LANCZOS, 1);

        // Copy opacity mask
        $base->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);

        // Add overlay
        $base->compositeImage($over, Imagick::COMPOSITE_DEFAULT, 0, 0);

        $base->writeImage('output.png');
        header("Content-Type: image/png");

        echo $base;


        ////////


        // Set image path
        $path = '/path/to/your/images/';

        // Create new objects from png's
        $dude = new Imagick($path . 'dude.png');
        $mask = new Imagick($path . 'dudemask.png');

        // IMPORTANT! Must activate the opacity channel
        // See: http://www.php.net/manual/en/function.imagick-setimagematte.php
        $dude->setImageMatte(1); 

        // Create composite of two images using DSTIN
        // See: http://www.imagemagick.org/Usage/compose/#dstin
        $dude->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0);

        // Write image to a file.
        $dude->writeImage($path . 'newimage.png');

        // And/or output image directly to browser
        header("Content-Type: image/png");
        echo $dude;
    }
}
