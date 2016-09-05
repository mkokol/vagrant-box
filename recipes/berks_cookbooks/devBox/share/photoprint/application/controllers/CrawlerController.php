<?php

class CrawlerController extends Helpers_General_ControllerAction
{
    private $scheme;
    private $httpHost;
    private $pagesInfo = [];
    private $notCrawledLinks = [];

    public function indexAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        $this->scheme = $this->_request->getScheme();
        $this->httpHost = $this->_request->getHttpHost();

        $pageLinks = $this->parseUrl('http://photoprint/ru', 0);
        $this->parseList($pageLinks, 1);

        echo '<pre>';
        print_r($this->pagesInfo);
        die;
    }

    public function parseList($pageLinks, $level)
    {
        $subLinksCollections = [];
        foreach ($pageLinks as $link) {
            if (strpos($link, 'javascript') === false && (strpos($link, 'mailto') === false)) {
                if (strpos($link, '/') === 0) {
                    $fullLink = $this->scheme . '://' . $this->httpHost . $link;
                    $subLinksCollections[] = $this->parseUrl($fullLink, $level);
                } else {
                    $this->notCrawledLinks[] = $link;
                }
            }
        }

        foreach ($subLinksCollections as $subLinks) {
            $this->parseList($subLinks, $level + 1);
        }
    }

    private function parseUrl($url, $level)
    {
        if (isset($this->pagesInfo[$url])) {
            return [];
        }

        echo $url . '<br>';

        $this->pagesInfo[$url]['level'] = $level;

        $client = new Zend_Http_Client(
            $url,
            [
                'maxredirects' => 0,
                'timeout'      => 30
            ]
        );
        $response = $client->request();
        $html = $response->getBody();

        if($response->getStatus() !== 200) {
            echo 'error' . '--------------------<br>';
            echo $url . '<br>';
            echo 'error' . '--------------------<br>';

            return [];
        }

        $dom = new Zend_Dom_Query(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'utf-8')
        );

        $title = $dom->query('title');
        $this->pagesInfo[$url]['title'] = (is_object($title->current())) ? trim($title->current()->textContent) : 'null';


        $metaTags = $dom->query('meta'); //This is not a correct query (does anyone have an alternative?)
        foreach ($metaTags as $meta) {
            if ($meta->getAttribute('name') == 'description') {
                $this->pagesInfo[$url]['description'] = trim($meta->getAttribute('content'));
            }
            if ($meta->getAttribute('name') == 'keywords') {
                $this->pagesInfo[$url]['keywords'] = trim($meta->getAttribute('content'));
            }
        }

        $h1 = $dom->query('h1');
        $this->pagesInfo[$url]['h1'] = (is_object($h1->current())) ? trim($h1->current()->textContent) : 'null';

        $links = $dom->query('a');
        $allLinks = [];

        foreach ($links as $link) {
            $allLinks[] = $link->getAttribute('href');
        }

        $this->pagesInfo[$url]['links'] = $allLinks;

        $filteredLinks = [];
        foreach ($allLinks as $link) {
            if (!isset($this->pagesInfo[$link])) {
                $filteredLinks[] = $link;

                if(strpos($link, 'ruuser') !== false){
                    echo 'Error on page:' . '<br>';
                    echo $url . '<br>';
                    echo 'Links:' . '<br>';
                    echo $link . '<br>';
                    die;
                }
            }
        }

        return $filteredLinks;
    }
}
