<?php

class Helpers_View_Image extends Helpers_View_H
{

    public function path($imgName, $size, $extension, $origin = false)
    {
        echo Images::getImagePath($this->baseUrl, $imgName, $size, $extension, $origin);
    }

    public function avatar($imgName, $size, $extension)
    {
        echo Images::getAvatarPath($this->baseUrl, $imgName, $size, $extension);
    }

    public function getPath($imgName, $size, $extension, $origin = false)
    {
        return Images::getImagePath($this->baseUrl, $imgName, $size, $extension, $origin);
    }

    public function imgExist($itemXml, $imgType)
    {
        if ($itemXml) {
            $img = $itemXml->xpath("//img[@type=\"{$imgType}\"]");
        }
        return (isset($img[0])) ? true : false;
    }

    public function getInBoxImg($itemXml, $imgType, $method = 'print')
    {
        if ($itemXml)
            $img = $itemXml->xpath("//img[@type=\"{$imgType}\"]");
        if (!isset($img[0])) {
            $imgHtml = "<img src=\"$this->baseUrl/public/theme/produces/none.png\"/>";
        } else {
            $img = $img[0];
            $width = $img->width;
            $height = $img->height;
            $marginTop = $img->top;
            $marginLeft = $img->left;
            $src = $img->url;
            $imgHtml = "<img src=\"{$src}\" style=\"width:{$width}px; height:{$height}px; margin-top:{$marginTop}px; margin-left:{$marginLeft}px;\"/>";
        }
        if ($method == 'print') {
            echo $imgHtml;
        } else if ($method == 'get') {
            return $imgHtml;
        }
    }

    public function getSmallInBoxImgPreview($delta, $itemXml, $imgType, $alt = '', $deltaTop = 0, $deltaLeft = 0)
    {
        $this->getSmallInBoxImg('s', $delta, $itemXml, $imgType, $alt, $deltaTop, $deltaLeft);
    }

    public function getSmallInBoxImgView($delta, $itemXml, $imgType, $alt = '', $deltaTop = 0, $deltaLeft = 0)
    {
        $this->getSmallInBoxImg('p', $delta, $itemXml, $imgType, $alt, $deltaTop, $deltaLeft);
    }

    private function getSmallInBoxImg($size, $delta, $itemXml, $imgType, $alt, $deltaTop, $deltaLeft)
    {
        if ($itemXml) {
            $img = $itemXml->xpath("//img[@type=\"{$imgType}\"]");
        }
        if (!isset($img[0])) {
            echo "<img src=\"$this->baseUrl/public/theme/produces/none.png\"/>";
        } else {
            $img = $img[0];
            $width = round($img->width / $delta);
            $height = round($img->height / $delta);
            $marginTop = round($img->top / $delta) + $deltaTop;
            $marginLeft = round($img->left / $delta) + $deltaLeft;
            $urlSrc = explode('/images/', $img->url);
            if (isset($urlSrc[1])) {
                $urlSrcPath = $urlSrc[1];
                $urlSrcPath = str_replace('.p.', ".$size.", $urlSrcPath);
                $src = $this->baseUrl . '/images/' . $urlSrcPath;
                if ($this->baseUrl == '/p') {
                    $src = 'http://photoprint.in.ua/images/' . $urlSrcPath;
                }
                $imgAlt = ($alt) ? "alt=\"{$alt}\"" : '';
                echo "<img src=\"{$src}\" itemprop=\"image\" {$imgAlt} class=\"printData\" style=\"width:{$width}px; height:{$height}px; margin-top:{$marginTop}px; margin-left:{$marginLeft}px;\"/>";
            }
        }
    }

    public function buildImgHolder($imgCode, $imgNum, $itemXml, $sizeStyle, $borderImg, $showUploadHelper = true, $showImageBorder = true)
    {
        $imgUrl = $this->getInBoxImg($itemXml, $imgCode, 'get');
        $imgUrl = str_replace('<img src=', '<img class="printData" src=', $imgUrl);
        $imgHolder =
            '<div class="insert-img size" id="insert-img' . $imgNum . '">'
            . '<div class="box size">'
            . ((!$this->imgExist($itemXml, $imgCode) && $showUploadHelper) ? $this->imgUploadHelper($sizeStyle) : '')
            . '<img class="content-block ' . $sizeStyle . '" src="' . $this->baseUrl . '/public/theme/transparent.png"/>'
            . (($showImageBorder) ? '<img class="' . $sizeStyle . ' content-image-border" src="' . $this->baseUrl . '/public/theme/produces/' . $borderImg . '">' : '')
            . $imgUrl
            . '</div>'
            . '</div>';
        echo $imgHolder;
    }

    private function imgUploadHelper($sizeStyle)
    {
        if ($this->t == null) {
            $this->t = $view = Zend_Layout::startMvc()->getView()->t;
        }
        $message = '';
        if ($sizeStyle == 'size-origin-cup-long' || $sizeStyle == 'size-origin-cup-double' || $sizeStyle == 'size-origin-puzzle'
            || $sizeStyle == 'size-origin-mousepad-circle' || $sizeStyle == 'size-origin-mousepad-square'
        ) {
            $message = '<p>' . $this->t->_('click')
                . ' <a href="' . $this->baseUrl . '/' . $this->lang . '/user/uploadimageswnd" class="upload-img">' . $this->t->_('here') . '</a> '
                . $this->t->_('for_dawnload') . '</p>' . '<p>' . $this->t->_('or_dawnload') . '</p>';
        } else if ($sizeStyle == 'size-origin-trivet' || $sizeStyle == 'size-origin-postcard' || $sizeStyle == 'size-origin-tshirt') {

            $message = '<p>' . $this->t->_('click')
                . ' <a href="' . $this->baseUrl . '/' . $this->lang . '/user/uploadimageswnd" class="upload-img">' . $this->t->_('here') . '</a> '
                . $this->t->_('for_dawnload') . '</p>'
                . '<p>' . $this->t->_('you_photo') . '</p>'
                . '<p>' . $this->t->_('or_draganddrop') . '</p>'
                . '<p>' . $this->t->_('top_panel') . '</p>';
        }
        return
            '<div class="start-txt size">'
            . $message
            . '</div>';
    }

    public function getColorInfo($itemXml, $color)
    {
        if (!$itemXml) {
            $selected = ($color == 'white') ? ' selected' : '';

            echo '<span class="' . $color . $selected . '" data-color="' . $color . '" data-img-config=""></span>';
            return;
        }

        $colorImages = $itemXml->xpath("//$color");
        $colorImageConfig = '';

        if (count($colorImages)) {
            $colorImageConfig = [];
            $imgTypes = ['front', 'back'];
            foreach ($imgTypes as $imgType) {
                $img = $itemXml->xpath("//$color//img[@type=\"{$imgType}\"]");
                if (isset($img[0])) {
                    $img = $img[0];
                    $colorImageConfig[$imgType] = [
                        'width'  => (integer) $img->width,
                        'height' => (integer) $img->height,
                        'top'    => (integer) $img->top,
                        'left'   => (integer) $img->left,
                        'src'    => (string) $img->url
                    ];
                }
            }
            $colorImageConfig = (string) json_encode($colorImageConfig);
            $colorImageConfig = htmlspecialchars($colorImageConfig);
        }

        $selectedColor = ('' != (string) $itemXml->color) ? (string) $itemXml->color : 'white';

        $selected = '';
        if ($color == $selectedColor) {
            $selected .= ' selected';
        }

        echo '<span class="' . $color . $selected . '" data-color="' . $color . '" data-img-config="' . $colorImageConfig . '"></span>';
    }
}
