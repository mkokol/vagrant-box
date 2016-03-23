<?php

trait Helpers_General_Traits_Breadcrumbs
{
    private $breadcrumbs = [
        0 => [
            'name' => 'main_page_link',
            'link' => [
                'url'    => '/',
                'params' => []
            ]
        ]
    ];

    protected function addBreadcrumbsLevel($name, $url = null, $urlParams = [])
    {
        $item = ['name' => $name];

        if ($url) {
            $item['link'] = [
                'url'    => $url,
                'params' => $urlParams
            ];
        }

        $this->breadcrumbs[] = $item;
    }

    protected function addBreadcrumbsLevelFromArray($arrayItems, $item)
    {
        if (count($arrayItems) != 1) {
            return;
        }

        $keys = array_keys($arrayItems);

        if (isset($arrayItems[$keys[0]])) {
            $language = Helpers_General_UrlManager::getLanguage();

            $breadcrumbUrl = $arrayItems[$keys[0]]['seo_url'];
            if (!$arrayItems[$keys[0]]['seo_url']) {
                $breadcrumbUrl = "/produces/catalog/item/{$item}?themeId={$arrayItems[$keys[0]]['id']}";
            }

            $this->addBreadcrumbsLevel(
                $arrayItems[$keys[0]][$language],
                $breadcrumbUrl
            );
        }
    }
}
