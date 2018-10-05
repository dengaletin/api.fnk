<?php

namespace app\components\parser;


use Symfony\Component\DomCrawler\Crawler;

class FinamParser extends AbstractParser
{
    const URL = 'https://www.finam.ru/analysis/conews/rsspoint';
    const TIMEZONE = '+0300';
    const ITEM_SELECTOR = 'rss>channel>item';
    const LINK_SELECTOR = 'link';
    const DATE_SELECTOR = 'pubDate';
    const TITLE_SELECTOR = 'title';
    const ARTICLE_SELECTOR = '.f-newsitem-text';


    function fetch($url)
    {
        $this->attachFile();
        $crawler = new Crawler();
        if ($url == static::URL) {
            $crawler->addXmlContent(file_get_contents($url));
        } else {
            $crawler->addHtmlContent(file_get_contents($url), 'windows-1251');
        }
        return $crawler;
    }
}