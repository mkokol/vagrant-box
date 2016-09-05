<?php

class Helpers_View_Product extends Helpers_View_H
{
    private $productImgParam = [
        'i' => [
            'mantshirt'   => [
                'width'  => 100,
                'height' => 122
            ],
            'womantshirt' => [
                'width'  => 100,
                'height' => 122
            ],
            'a5'          => [
                'width'  => 100,
                'height' => 71
            ],
            'a4'          => [
                'width'  => 100,
                'height' => 71
            ],
            '130x180'     => [
                'width'  => 100,
                'height' => 71
            ],
            'white'       => [
                'width'  => 100,
                'height' => 92,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'glass'       => [
                'width'  => 100,
                'height' => 92,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'magic'       => [
                'width'  => 100,
                'height' => 92,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'glassmagic'  => [
                'width'  => 100,
                'height' => 92,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'cork'        => [
                'width'  => 207,
                'height' => 207
            ],
            'ceramic'     => [
                'width'  => 207,
                'height' => 207
            ],
            'rubber'      => [
                'width'  => 207,
                'height' => 207
            ],
            'square'      => [
                'width'  => 207,
                'height' => 180
            ],
            'circle'      => [
                'width'  => 207,
                'height' => 207
            ],
            'double'      => [
                'width'  => 171,
                'height' => 252
            ]
        ],
        'p' => [
            'mantshirt'   => [
                'width'  => 207,
                'height' => 252
            ],
            'womantshirt' => [
                'width'  => 207,
                'height' => 252
            ],
            'a5'          => [
                'width'  => 207,
                'height' => 157
            ],
            'a4'          => [
                'width'  => 207,
                'height' => 157
            ],
            '130x180'     => [
                'width'  => 207,
                'height' => 157
            ],
            'white'       => [
                'width'  => 207,
                'height' => 190,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'glass'       => [
                'width'  => 207,
                'height' => 190,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'magic'       => [
                'width'  => 207,
                'height' => 190,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'glassmagic'  => [
                'width'  => 207,
                'height' => 190,
                'main'   => [
                    'width'  => 207,
                    'height' => 132
                ]
            ],
            'cork'        => [
                'width'  => 207,
                'height' => 207
            ],
            'ceramic'     => [
                'width'  => 207,
                'height' => 207
            ],
            'rubber'      => [
                'width'  => 207,
                'height' => 207
            ],
            'square'      => [
                'width'  => 207,
                'height' => 180
            ],
            'circle'      => [
                'width'  => 207,
                'height' => 207
            ],
            'double'      => [
                'width'  => 171,
                'height' => 252
            ]
        ],
        'o' => [
            'mantshirt'   => [
                'width'  => 462,
                'height' => 548
            ],
            'womantshirt' => [
                'width'  => 462,
                'height' => 548
            ],
            'a5'          => [
                'width'  => 440,
                'height' => 312
            ],
            'a4'          => [
                'width'  => 440,
                'height' => 312
            ],
            '130x180'     => [
                'width'  => 440,
                'height' => 312
            ],
            'white'       => [
                'width'  => 328,
                'height' => 295,
                'main'   => [
                    'width'  => 440,
                    'height' => 282,
                ]
            ],
            'glass'       => [
                'width'  => 328,
                'height' => 295,
                'main'   => [
                    'width'  => 440,
                    'height' => 282
                ]
            ],
            'magic'       => [
                'width'  => 328,
                'height' => 295,
                'main'   => [
                    'width'  => 440,
                    'height' => 282
                ]
            ],
            'glassmagic'  => [
                'width'  => 328,
                'height' => 295,
                'main'   => [
                    'width'  => 440,
                    'height' => 282
                ]
            ],
            'cork'        => [
                'width'  => 300,
                'height' => 300
            ],
            'ceramic'     => [
                'width'  => 300,
                'height' => 300
            ],
            'rubber'      => [
                'width'  => 300,
                'height' => 300
            ],
            'square'      => [
                'width'  => 440,
                'height' => 383
            ],
            'circle'      => [
                'width'  => 440,
                'height' => 440
            ],
            'double'      => [
                'width'  => 300,
                'height' => 430
            ]
        ]
    ];

    public function getTypeItems($group)
    {
        switch ($group) {
            case 'postcard':
                return  ['double'];

            case 'puzzle':
                return ['a5', '130x180', 'a4'];

            case 'mousepad':
                return ['square', 'circle'];

            case 'trivet':
                return ['ceramic', 'rubber', 'cork'];

            case 'cup':
                return ['glass', 'white', 'glassmagic', 'magic'];

            case 'tshirt':
                return ['mantshirt', 'womantshirt'];
        }

        return [];
    }

    public function baseItem($group)
    {
        // TODO: make this dynamic NOT HARDCODED
        $item = '';
        switch ($group) {
            case 'postcard':
                $item = 'double';
                break;
            case 'puzzle':
                $item = 'a5';
                break;
            case 'mousepad':
                $item = 'square';
                break;
            case 'trivet':
                $item = 'ceramic';
                break;
            case 'cup':
                $item = 'glass';
                break;
            case 'tshirt':
                $item = 'mantshirt';
                break;
        }

        return $item;
    }

    public function getImg($t, $item, $product, $size, $side = null, $color = null)
    {
        list($groupName, $productImgType) = $this->getGroupNameAndProductImgType($product);
        $dataXml = $this->parseProductXml($product);
        $side = $this->getProductSide($side, $groupName, $dataXml);
        $color = $this->getProductColor($color, $groupName, $product, $dataXml);
        $item = ($item === null) ? $this->baseItem($groupName) : $item;

        $imgAlt = (isset($product[$this->lang])) ? $t->_($item) . '. ' . $product[$this->lang] : $t->_($item) . '.';
        $imgSrc = $this->getImgUrl($item, $product, $side, $size, $color, $productImgType);
        $imgParam = isset($this->productImgParam[$size][$item][$side])
            ? $this->productImgParam[$size][$item][$side]
            : $this->productImgParam[$size][$item];

        $style = ' style="max-width:' . $imgParam['width'] . 'px"';

        return '<img src="' . $imgSrc . '" itemprop="image" alt="' . $imgAlt . '"' . $style
            . ' width="100%" height="auto" />';
    }

    public function getImgUrl($item, $product, $side, $size, $color, $productImgType = 'item')
    {
        $imgName = "{$item}-{$product['id']}_{$side}";
        if ($color) {
            $imgName .= "_{$color}";
        }
        $imgName .= ".$size.jpg";

        // TODO: Should be always updated
        $date = isset($product['updated']) ? $product['updated'] : $product['date'];

        $md5Prefix = substr(md5($product['id'] . $date), 0, 3);
        $sha1Prefix = substr(sha1($product['id'] . $date), 0, 3);

        return $this->baseUrl . "/public/products/$md5Prefix/$sha1Prefix/$productImgType/$imgName";
    }

    public function getFullImgUrl($item, $product, $side = null, $size = 'o', $color = null, $productImgType = 'item')
    {
        list($groupName, $productImgType) = $this->getGroupNameAndProductImgType($product);
        $dataXml = $this->parseProductXml($product);
        $side = $this->getProductSide($side, $groupName, $dataXml);
        $color = $this->getProductColor($color, $groupName, $product, $dataXml);

        $imgUrl = $this->getImgUrl($item, $product, $side, $size, $color, $productImgType);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $domainWithSchema = $request->getScheme() . '://' . $request->getHttpHost();

        return $domainWithSchema . $imgUrl;
    }

    public function getFullImgPath($t, $item, $product, $size, $side = null, $color = null)
    {
        list($groupName, $productImgType) = $this->getGroupNameAndProductImgType($product);
        $dataXml = $this->parseProductXml($product);
        $side = $this->getProductSide($side, $groupName, $dataXml);
        $color = $this->getProductColor($color, $groupName, $product, $dataXml);
        $item = ($item === null) ? $this->baseItem($groupName) : $item;

        $imgAlt = (isset($product[$this->lang])) ? $t->_($item) . '. ' . $product[$this->lang] : $t->_($item) . '.';
        $imgSrc = $this->getImgUrl($item, $product, $side, $size, $color, $productImgType);
        $imgParam = isset($this->productImgParam[$size][$item][$side])
            ? $this->productImgParam[$size][$item][$side]
            : $this->productImgParam[$size][$item];

        $style = ' style="max-width:' . $imgParam['width'] . 'px"';

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $domainWithSchema = $request->getScheme() . '://' . $request->getHttpHost();

        return '<img src="' . $domainWithSchema . $imgSrc . '" itemprop="image" alt="' . $imgAlt . '"' . $style
            . ' width="100%" height="auto" />';
    }

    private function getGroupNameAndProductImgType($product)
    {
        $groupName = '';
        $productImgType = '';

        if (isset($product['product_group'])) {
            $groupName = $product['product_group'];
            $productImgType = 'basket-item';
        } else if (isset($product['group_name'])) {
            $groupName = $product['group_name'];
            $productImgType = 'item';
        }

        return [$groupName, $productImgType];
    }

    private function parseProductXml($product)
    {
        if(isset($product['content_xml'])) {
            return (is_object($product['content_xml']))
                ? $product['content_xml']
                : new SimpleXMLElement(stripslashes($product['content_xml']));
        }

        return new SimpleXMLElement(stripslashes($product['dataXml']));
    }

    private function getProductSide($side, $groupName, $dataXml)
    {
        if ($side == null) {
            $side = 'main';

            if ($groupName == 'tshirt') {
                $side = 'front';
            }

            if ($groupName == 'cup') {
                $itemTemplate = (string)$dataXml->template;
                $side = ($itemTemplate == 4) ? 'main' : 'left';
            }
        }

        return $side;
    }

    private function getProductColor($color, $groupName, $product, $dataXml)
    {
        if ($color == null && $groupName == 'tshirt') {
            if ($dataXml->color) {
                $color = (string)$dataXml->color;
            } else {
                $color = (isset($product['color']) && $product['color'] != '') ? $product['color'] : 'white';
            }
        }

        return $color;
    }
}
