<?php

namespace app\components\parser;


use Symfony\Component\DomCrawler\Crawler;
use yii\helpers\StringHelper;

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
        $body = file_get_contents($url, false, $this->prepareContext());
        $c = new Crawler();
        if (StringHelper::startsWith($url, 'https://www.rbc.ru/'))
        {
            $c->addHtmlContent($body);
            return $c;
        }

        $c->addXmlContent($body);
        return $c;
    }
}