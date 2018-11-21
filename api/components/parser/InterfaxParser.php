<?php

namespace app\components\parser;


use Symfony\Component\DomCrawler\Crawler;

class InterfaxParser extends AbstractParser
{
    const URL = 'https://www.interfax.ru/rss.asp';
    const TIMEZONE = '+0300';
    const ITEM_SELECTOR = 'rss>channel>item';
    const LINK_SELECTOR = 'link';
    const DATE_SELECTOR = 'pubDate';
    const TITLE_SELECTOR = 'title';
    const ARTICLE_SELECTOR = 'article';

    function fetch($url)
    {
        return new Crawler(file_get_contents($url));
    }

    protected function prepareText($text)
    {
        $text = preg_replace('/^.*INTERFAX.RU\s-/i', '', $text);
        $text = trim($text);
        return parent::prepareText($text);
    }

}