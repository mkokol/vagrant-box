<?php

class SeofilesController extends Helpers_General_ControllerAction
{

    public function init()
    {
    }

    public function sitemapxmlAction()
    {
        $this->getResponse()->setHeader('content-type', 'text/xml', true);

        $seoRoute = new SeoRoute();

        $content = [
            ['loc' => 'http://photoprint.in.ua/{{lang}}', 'priority' => '1.00'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/produces/products/group/postcard', 'priority' => '0.8'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/produces/products/group/puzzle', 'priority' => '0.8'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/produces/products/group/trivet', 'priority' => '0.8'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/produces/products/group/mousepad', 'priority' => '0.8'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/produces/products/group/tshirt', 'priority' => '0.8'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/info/payment', 'priority' => '0.64'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/info/transportation', 'priority' => '0.64'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/info/help', 'priority' => '0.64'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/info/designers', 'priority' => '0.64'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/info/developers', 'priority' => '0.64'],
            ['loc' => 'http://photoprint.in.ua/{{lang}}/info/business', 'priority' => '0.64'],
        ];

        $contentTheme = $seoRoute->getSiteMapXmlThemes();
        foreach ($contentTheme as $theme) {
            if ($theme['seo_url']) {
                $loc = "http://photoprint.in.ua{$theme['seo_url']}";
            } else {
                $loc = "http://photoprint.in.ua/{{lang}}/produces/catalog/item/{$theme['product_item']}";
                $loc .= ($theme['theme_id']) ? "?themeId={$theme['theme_id']}" : '';
            }
            $content[] = ['loc' => $loc, 'priority' => '0.64'];
        }
        unset($contentTheme);

        $contentTag = $seoRoute->getSiteMapXmlSubThemes();
        foreach ($contentTag as $tag) {
            if ($tag['tag_seo_url']) {
                $loc = "http://photoprint.in.ua{$tag['tag_seo_url']}";
            } elseif ($tag['theme_seo_url']) {
                $loc = "http://photoprint.in.ua{$tag['theme_seo_url']}";
                $loc .= ($tag['tag_id']) ? "?tagId={$tag['tag_id']}" : '';
            } else {
                $loc = "http://photoprint.in.ua/{{lang}}/produces/catalog/item/{$tag['product_item']}";
                $loc .= ($tag['theme_id']) ? "?themeId={$tag['theme_id']}" : '';
                $loc .= ($tag['tag_id']) ? "&amp;tagId={$tag['tag_id']}" : '';
            }
            $content[] = ['loc' => $loc, 'priority' => '0.64'];
        }
        unset($contentSubTheme);

        $contentItems = $seoRoute->getSiteMapXmlItems();
        foreach ($contentItems as $item) {
            if ($item['item_seo_url']) {
                $loc = "http://photoprint.in.ua{$item['item_seo_url']}";
            } else {
                $loc = "http://photoprint.in.ua/{{lang}}/produces/catalog/item/{$item['product_item']}";
                $loc .= ($item['item_id']) ? "?id={$item['item_id']}" : '';
            }
            $content[] = ['loc' => $loc, 'priority' => '0.64'];
        }
        unset($contentItems);

        $this->view->content = $content;
    }

    public function robotstxtAction()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody(
                "User-agent: *\n"
                . "Allow: /\n"
                . "Disallow: /ru/index\n"
                . "Disallow: /ua/index\n"
                . "Disallow: /ru/user\n"
                . "Disallow: /ua/user\n"
                . "Disallow: /ru/partnership\n"
                . "Disallow: /ua/partnership\n"
                . "Disallow: /ru/produces/productpreview\n"
                . "Disallow: /ua/produces/productpreview\n"
                . "Disallow: /ua/produces/previewitem\n"
                . "Disallow: /ru/produces/previewitem\n"
                . "Disallow: /ru/produces/create\n"
                . "Disallow: /ua/produces/create\n"
                . "Disallow: /ru/search\n"
                . "Disallow: /ua/search\n"
            );
    }

    public function google460abbf5176c8cf7Action()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody('google-site-verification: google460abbf5176c8cf7.html');
    }

    public function google819421d3d7a5512fAction()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody('google-site-verification: google819421d3d7a5512f.html');
    }

    public function yandex7342dde8071d5000Action()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody(
                "<html>\n"
                . "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head>\n"
                . "<body>Verification: 7342dde8071d5000</body>\n"
                . "</html>\n"
            );
    }

    public function yandex7342dde8071d5177Action()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody('');
    }

    public function yandex5314808c5fd4a7f5Action()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody('');
    }

    public function bingSiteAuthAction()
    {
        $this->removeDefaultView();
        $this->getResponse()
            ->setHeader('content-type', 'text/plain', true)
            ->setBody(
                "<?xml version=\"1.0\"?>\n".
                "<users>\n".
                    "<user>C0552DC82157E5AC8D7D173C45E70CDA</user>\n".
                "</users>\n"
            );
    }
}
