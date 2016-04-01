<?php

class Helpers_View_Basket
{
    public function __construct()
    {
    }

    public function showItemName($xmlDataStr)
    {
        $xmlData = new SimpleXMLElement(stripslashes($xmlDataStr));

        return (string) $xmlData->item;
    }

    public function showDeteils($t, $xmlDataStr)
    {
        $xmlData = new SimpleXMLElement(stripslashes($xmlDataStr));
        $templateId = (string) $xmlData->template;
        $group = (string) $xmlData->group;
        $size = (string) $xmlData->size;

        switch ($group) {
            case 'tshirt':
                if ($group && $templateId && $size) {
                    $productsTemplates = new ProductsTemplates();
                    $detailsItem = $productsTemplates->fetchRow("id = $templateId");
                    echo '<br/>' . $t->_('size') . ' ' . $size . '; ' . $t->_($detailsItem->name);
                }

                break;
        }
    }

    public function showPrice($item, $defaultItem = null)
    {
        return ProductsItems::getProductItemPrice($item, $defaultItem);
    }
}
