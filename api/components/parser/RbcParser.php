<?php

namespace app\components\parser;


use Symfony\Component\DomCrawler\Crawler;

class RbcParser extends AbstractParser
{
    function fetch($url)
    {
        return new Crawler(file_get_contents($url));
    }
}