<?php

namespace app\components\parser;


use Symfony\Component\DomCrawler\Crawler;

class RbcParser extends AbstractParser
{
    const RSS_IMAGE_SELECTOR = 'enclosure';

    public function fetchImage(Crawler $node)
    {
        $url = null;
        try
        {
            $url = $node->filter('enclosure')->attr('url');
        } catch (\InvalidArgumentException $exception)
        {}
        return $url;
    }

    function fetch($url)
    {
        return new Crawler(file_get_contents($url));
    }
}