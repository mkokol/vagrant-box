<?php

class PublicController extends Helpers_General_ControllerAction
{
    private $item;
    private $itemId;
    private $size;
    private $side = null;
    private $color = null;

    private $prodConf = [
        // tshirt
        'mantshirt'   => [
            'width'    => 190,
            'height'   => 250,
            'position' => [
                'left' => 126,
                'top'  => 90
            ],
            'p'        => [
                'width'  => 207,
                'height' => 252
            ],
            'i'        => [
                'width'  => 100,
                'height' => 122
            ]
        ],
        'womantshirt' => [
            'width'    => 190,
            'height'   => 250,
            'position' => [
                'left' => 130,
                'top'  => 90
            ],
            'p'        => [
                'width'  => 207,
                'height' => 252
            ],
            'i'        => [
                'width'  => 100,
                'height' => 122
            ]
        ],

        // pazzle
        'a5'          => [
            'width'  => 440,
            'height' => 312,
            'delta'  => 1.3,
            'p'      => [
                'width'  => 207,
                'height' => 147
            ],
            'i'      => [
                'width'  => 100,
                'height' => 71
            ]
        ],
        'a4'          => [
            'width'  => 440,
            'height' => 312,
            'delta'  => 1.3,
            'p'      => [
                'width'  => 207,
                'height' => 147
            ],
            'i'      => [
                'width'  => 100,
                'height' => 71
            ]
        ],
        '130x180'     => [
            'width'  => 440,
            'height' => 312,
            'delta'  => 1.3,
            'p'      => [
                'width'  => 207,
                'height' => 147
            ],
            'i'      => [
                'width'  => 100,
                'height' => 71
            ]
        ],

        // cup
        'white'       => [
            'width'    => 190,
            'height'   => 210,
            'main'     => [
                'width'  => 612,
                'height' => 353,
                'o'      => [
                    'width'  => 440,
                    'height' => 282,
                    'border' => [
                        'top' => 85
                    ]
                ],
                'p'      => [
                    'width'  => 207,
                    'height' => 132,
                    'border' => [
                        'top' => 40
                    ]
                ],
                'i'      => [
                    'width'  => 100,
                    'height' => 64,
                    'border' => [
                        'top' => 19
                    ]
                ]
            ],
            'position' => [
                'left'  => [
                    'left' => 98,
                    'top'  => 50
                ],
                'right' => [
                    'left' => 36,
                    'top'  => 50
                ],
                'main'  => [
                    'left' => 0,
                    'top'  => 119
                ]
            ],
            'p'        => [
                'width'  => 207,
                'height' => 190
            ],
            'i'        => [
                'width'  => 100,
                'height' => 92
            ]
        ],
        'glass'       => [
            'width'    => 190,
            'height'   => 210,
            'main'     => [
                'width'  => 612,
                'height' => 353,
                'o'      => [
                    'width'  => 440,
                    'height' => 282,
                    'border' => [
                        'top' => 85
                    ]
                ],
                'p'      => [
                    'width'  => 207,
                    'height' => 132,
                    'border' => [
                        'top' => 40
                    ]
                ],
                'i'      => [
                    'width'  => 100,
                    'height' => 64,
                    'border' => [
                        'top' => 19
                    ]
                ]
            ],
            'position' => [
                'left'  => [
                    'left' => 98,
                    'top'  => 50
                ],
                'right' => [
                    'left' => 36,
                    'top'  => 50
                ],
                'main'  => [
                    'left' => 0,
                    'top'  => 119
                ]
            ],
            'p'        => [
                'width'  => 207,
                'height' => 190
            ],
            'i'        => [
                'width'  => 100,
                'height' => 92
            ]
        ],
        'magic'       => [
            'width'    => 190,
            'height'   => 210,
            'main'     => [
                'width'  => 612,
                'height' => 353,
                'o'      => [
                    'width'  => 440,
                    'height' => 282,
                    'border' => [
                        'top' => 85
                    ]
                ],
                'p'      => [
                    'width'  => 207,
                    'height' => 132,
                    'border' => [
                        'top' => 40
                    ]
                ],
                'i'      => [
                    'width'  => 100,
                    'height' => 64,
                    'border' => [
                        'top' => 19
                    ]
                ]
            ],
            'position' => [
                'left'  => [
                    'left' => 98,
                    'top'  => 50
                ],
                'right' => [
                    'left' => 36,
                    'top'  => 50
                ],
                'main'  => [
                    'left' => 0,
                    'top'  => 119
                ]
            ],
            'p'        => [
                'width'  => 207,
                'height' => 190
            ],
            'i'        => [
                'width'  => 100,
                'height' => 92
            ]
        ],
        'glassmagic'  => [
            'width'    => 190,
            'height'   => 210,
            'main'     => [
                'width'  => 612,
                'height' => 353,
                'o'      => [
                    'width'  => 440,
                    'height' => 282,
                    'border' => [
                        'top' => 85
                    ]
                ],
                'p'      => [
                    'width'  => 207,
                    'height' => 132,
                    'border' => [
                        'top' => 40
                    ]
                ],
                'i'      => [
                    'width'  => 100,
                    'height' => 64,
                    'border' => [
                        'top' => 19
                    ]
                ]
            ],
            'position' => [
                'left'  => [
                    'left' => 98,
                    'top'  => 50
                ],
                'right' => [
                    'left' => 36,
                    'top'  => 50
                ],
                'main'  => [
                    'left' => 0,
                    'top'  => 119
                ]
            ],
            'p'        => [
                'width'  => 207,
                'height' => 190
            ],
            'i'        => [
                'width'  => 100,
                'height' => 92
            ]
        ],

        // trivet
        'cork'        => [
            'width'  => 300,
            'height' => 300,
            'p'      => [
                'width'  => 207,
                'height' => 207
            ],
            'i'      => [
                'width'  => 100,
                'height' => 100
            ]
        ],
        'ceramic'     => [
            'width'  => 300,
            'height' => 300,
            'p'      => [
                'width'  => 207,
                'height' => 207
            ],
            'i'      => [
                'width'  => 100,
                'height' => 100
            ]
        ],
        'rubber'      => [
            'width'  => 300,
            'height' => 300,
            'p'      => [
                'width'  => 207,
                'height' => 207
            ],
            'i'      => [
                'width'  => 100,
                'height' => 100
            ]
        ],

        // mousepad
        'square'      => [
            'width'  => 440,
            'height' => 383,
            'delta'  => 1.3,
            'p'      => [
                'width'  => 207,
                'height' => 180
            ],
            'i'      => [
                'width'  => 100,
                'height' => 87
            ]
        ],
        'circle'      => [
            'width'    => 440,
            'height'   => 440,
            'delta'    => 1.3,
            'position' => [
                'left' => 0,
                'top'  => 33
            ],
            'p'        => [
                'width'  => 207,
                'height' => 207
            ],
            'i'        => [
                'width'  => 100,
                'height' => 100
            ]
        ],

        // postcard
        'double'      => [
            'width'  => 300,
            'height' => 430,
            'p'      => [
                'width'  => 171,
                'height' => 252
            ],
            'i'      => [
                'width'  => 100,
                'height' => 147
            ]
        ]
    ];

    public function productsAction()
    {
        $productType = 'item';
        $item = strtolower($this->_request->getParam('item', ''));

        if(!$item){
            $productType = 'basket-item';
            $item = strtolower($this->_request->getParam('basket-item', ''));
        }

        $this->parseImageName($item);

        if($productType == 'item'){
            $productsItems = new ProductsItems();
            $productsItem = $productsItems->fetchRow('id = ' . $this->itemId);
        } else if($productType == 'basket-item'){
            $baskets = new Baskets();
            $productsItem = $baskets->fetchRow('id = ' . $this->itemId);
        }

        if (!isset($productsItem) || !$productsItem) {
            $this->error404();

            return;
        }

        $folder = strtolower($this->_request->getParam('folder', ''));

        if ($folder != 'test') {
            $date = isset($productsItem->updated) ? $productsItem->updated : $productsItem->date;
            $md5Prefix = substr(md5($productsItem->id . $date), 0, 3);
            $sha1Prefix = substr(sha1($productsItem->id . $date), 0, 3);

            if ($this->_request->getParam($md5Prefix, '') != $sha1Prefix) {
                $this->error404();

                return;
            }
        }

        $xmlStr = isset($productsItem->content_xml) ? $productsItem->content_xml : $productsItem->dataXml;
        $productXml = new SimpleXMLElement(stripslashes($xmlStr));

        $colorImg = $productXml->xpath("//{$this->color}/img[@type=\"{$this->side}\"]");
        if (isset($colorImg[0])) {
            $imgObj = $colorImg[0];
        } else {
            $imgObj = $productXml->xpath("//img[@type=\"{$this->side}\"]");
        }

        if (count($imgObj)) {
            $imgObj = $imgObj[0];
        }

        $image = $this->prepareImage($imgObj);
        /** @var Imagick $productImg */
        $productImg = $this->addBackground($image, $imgObj);
        $productImg = $this->addBorder($productImg);

        $productImg->setImageFormat('jpg');
        $productImg->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $productImg->setImageCompressionQuality(90);
        $productImg->stripImage();

        if ($folder != 'test') {
            $path = "public/products/{$md5Prefix}/{$sha1Prefix}/item";
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            $imgPath = "{$path}/{$item}";
            $productImg->writeImage($imgPath);
        }

        header("Content-Type: image/jpg");
        echo $productImg->getImageBlob();
        exit;
    }

    /**
     * @param $imgObj
     * @return Imagick
     */
    private function prepareImage($imgObj)
    {
        if (is_object($imgObj)) {
            $image = new Imagick((string) $imgObj->url);

            $width = (int) $imgObj->width;
            $height = (int) $imgObj->height;
            if (isset($this->prodConf[$this->item]['delta'])) {
                $width = round($width / $this->prodConf[$this->item]['delta']);
                $height = round($height / $this->prodConf[$this->item]['delta']);
            }

            $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1, true);

            $left = ((int) $imgObj->left < 0) ? -(int) $imgObj->left : 0;
            $top = ((int) $imgObj->top < 0) ? -(int) $imgObj->top : 0;
            list($width, $height) = $this->getImageSize();

            $image->cropImage($width, $height, $left, $top);
        }

        return $image;
    }

    /**
     * @param $image
     * @param $imgObj
     * @return Imagick
     */
    private function addBackground($image, $imgObj)
    {
        $bgImgUrl = $this->getBGImgPath();
        if ($bgImgUrl) {
            $productImg = new Imagick($bgImgUrl);
        } else {
            $productImg = new Imagick();
            list($width, $height) = $this->getImageSize();
            $productImg->newImage($width, $height, new ImagickPixel('white'));
            $productImg->setImageFormat('jpg');
        }

        if (isset($this->prodConf[$this->item]['position'][$this->side]) && is_array($this->prodConf[$this->item]['position'][$this->side])) {
            $globalPositionLeft = $this->prodConf[$this->item]['position'][$this->side]['left'];
            $globalPositionTop = $this->prodConf[$this->item]['position'][$this->side]['top'];
        } elseif (isset($this->prodConf[$this->item]['position']) && is_array($this->prodConf[$this->item]['position'])) {
            $globalPositionLeft = $this->prodConf[$this->item]['position']['left'];
            $globalPositionTop = $this->prodConf[$this->item]['position']['top'];
        } else {
            $globalPositionLeft = 0;
            $globalPositionTop = 0;
        }

        if (isset($image)) {
            list($left, $top) = $this->getImagePosition($imgObj);

            $productImg->compositeImage(
                $image,
                Imagick::COMPOSITE_DEFAULT,
                $globalPositionLeft + $left,
                $globalPositionTop + $top
            );
        }

        return $productImg;
    }

    private function addBorder($productImg)
    {
        $imgSizeParam = null;
        if (isset($this->prodConf[$this->item][$this->side][$this->size])) {
            $imgSizeParam = $this->prodConf[$this->item][$this->side][$this->size];
        }
        if (!$imgSizeParam && isset($this->prodConf[$this->item][$this->size])) {
            $imgSizeParam = $this->prodConf[$this->item][$this->size];
        }
        if ($imgSizeParam) {
            $productImg->resizeImage(
                $imgSizeParam['width'],
                $imgSizeParam['height'],
                Imagick::FILTER_LANCZOS,
                1
            );
        }

        $borderImgUrl = $this->getBorderImgPath();
        if ($borderImgUrl) {
            $left = (isset($imgSizeParam['border']['left'])) ? $imgSizeParam['border']['left'] : 0;
            $top = (isset($imgSizeParam['border']['top'])) ? $imgSizeParam['border']['top'] : 0;
            $borderImg = new Imagick($borderImgUrl);
            $productImg->compositeImage(
                $borderImg,
                Imagick::COMPOSITE_DEFAULT,
                $left,
                $top
            );
        }

        return $productImg;
    }

    private function getImageSize()
    {
        if (isset($this->prodConf[$this->item][$this->side]) && is_array($this->prodConf[$this->item][$this->side])) {
            $width = $this->prodConf[$this->item][$this->side]['width'];
            $height = $this->prodConf[$this->item][$this->side]['height'];
        } else {
            $width = $this->prodConf[$this->item]['width'];
            $height = $this->prodConf[$this->item]['height'];
        }

        return [$width, $height];
    }

    private function getImagePosition($imgObj)
    {
        $left = ((int) $imgObj->left > 0) ? (int) $imgObj->left : 0;
        $top = ((int) $imgObj->top > 0) ? (int) $imgObj->top : 0;

        if (isset($this->prodConf[$this->item]['delta'])) {
            $left = $left / $this->prodConf[$this->item]['delta'];
            $top = $top / $this->prodConf[$this->item]['delta'];
        }

        return [$left, $top];
    }

    private function parseImageName($item)
    {
        $itemParam = explode('-', $item);

        if (count($itemParam) !== 2) {
            $this->error404();

            return;
        }
        $this->item = $itemParam[0];

        $sizeParam = explode('.', $itemParam[1]);

        if (count($sizeParam) !== 3 || !in_array($sizeParam[1], ['o', 'p', 'i']) || $sizeParam[2] != 'jpg') {
            $this->error404();

            return;
        }
        $this->size = $sizeParam[1];

        $productParam = explode('_', $sizeParam[0]);
        $this->itemId = (int) $productParam[0];
        if (!$this->itemId) {
            $this->error404();

            return;
        }

        if (isset($productParam[1]) && !in_array($productParam[1], ['front', 'back', 'main', 'left', 'right'])) {
            $this->error404();

            return;
        } else if (isset($productParam[1])) {
            $this->side = $productParam[1];
        }

        if (isset($productParam[2]) && !in_array($productParam[2], ProductsItems::$tShirtColors)) {
            $this->error404();

            return;
        } else if (isset($productParam[2])) {
            $this->color = $productParam[2];
        }
    }

    private function getBGImgPath()
    {
        switch ($this->item) {
            case 'mantshirt' :
                return "public/theme/produces/tshirt/bg/{$this->item}_{$this->side}_{$this->color}.png";
            case 'womantshirt' :
                return "public/theme/produces/tshirt/bg/{$this->item}_{$this->side}_{$this->color}.png";

            case 'white' :
                return "public/theme/produces/cup/bg/{$this->side}.png";
            case 'glass' :
                return "public/theme/produces/cup/bg/{$this->side}.png";
            case 'magic' :
                return "public/theme/produces/cup/bg/{$this->side}.png";
            case 'glassmagic' :
                return "public/theme/produces/cup/bg/{$this->side}.png";
        }

        return '';
    }

    private function getBorderImgPath()
    {
        $bgImg = '';

        switch ($this->item) {
            case 'a5' :
                $bgImg = "public/theme/produces/puzzle/border/rectangle.{$this->size}.png";
                break;
            case 'a4' :
                $bgImg = "public/theme/produces/puzzle/border/rectangle.{$this->size}.png";
                break;
            case '130x180' :
                $bgImg = "public/theme/produces/puzzle/border/rectangle.{$this->size}.png";
                break;

            case 'white' :
                $bgImg = ($this->side == 'main') ? "public/theme/produces/cup/bg/rectangle.{$this->size}.png" : '';
                break;
            case 'glass' :
                $bgImg = ($this->side == 'main') ? "public/theme/produces/cup/bg/rectangle.{$this->size}.png" : '';
                break;
            case 'magic' :
                $bgImg = ($this->side == 'main') ? "public/theme/produces/cup/bg/rectangle.{$this->size}.png" : '';
                break;
            case 'glassmagic' :
                $bgImg = ($this->side == 'main') ? "public/theme/produces/cup/bg/rectangle.{$this->size}.png" : '';
                break;

            case 'cork' :
                $bgImg = "public/theme/produces/trivet/bg/square.{$this->size}.png";
                break;
            case 'ceramic' :
                $bgImg = "public/theme/produces/trivet/bg/circle.{$this->size}.png";
                break;
            case 'rubber' :
                $bgImg = "public/theme/produces/trivet/bg/square.{$this->size}.png";
                break;

            case 'square' :
                $bgImg = "public/theme/produces/mousepad/bg/square.{$this->size}.png";
                break;
            case 'circle' :
                $bgImg = "public/theme/produces/mousepad/bg/circle.{$this->size}.png";
                break;

            case 'double' :
                $bgImg = "public/theme/produces/postcard/bg/square.{$this->size}.png";
                break;
        }

        return $bgImg;
    }
}

