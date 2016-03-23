<?php

trait Helpers_General_Traits_Xml
{
    protected function parseXmlListToObject($xmlList)
    {
        foreach ($xmlList as $key => $xml) {
            $xmlList[$key]['content_xml'] = new SimpleXMLElement(stripslashes($xml['content_xml']));
        }
        return $xmlList;
    }
}
