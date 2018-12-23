<?php

namespace app\components\parser;


use app\components\parser\helpers\Candidates;
use app\components\parser\helpers\Common;
use app\components\parser\helpers\Factory;
use Symfony\Component\DomCrawler\Crawler;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

abstract class AbstractParser
{

    const URL = 'http://static.feed.rbc.ru/rbc/logical/footer/news.rss';
    const TIMEZONE = '+0300';
    const ITEM_SELECTOR = 'rss>channel>item';
    const LINK_SELECTOR = 'link';
    const DATE_SELECTOR = 'pubDate';
    const TITLE_SELECTOR = 'title';
    const ARTICLE_SELECTOR = '.article__text';
    const RSS_IMAGE_SELECTOR = null;


    private function isAlreadyParsed($link)
    {
        return (bool)((new \yii\db\Query())
            ->from('{{%parser_jobs}}')
            ->where(['source_url' => $link])
            ->count('id'));
    }

    private function buildMeta(Crawler $node)
    {
        $title = trim($node->filter(static::TITLE_SELECTOR)->eq(0)->text());
        $pub_date = $node->filter(static::DATE_SELECTOR)->eq(0)->text();
        $link = trim($node->filter(static::LINK_SELECTOR)->eq(0)->text());

        return [
            'title' => $title,
            'pubDate' => Common::mysqlDate($pub_date, static::TIMEZONE),
            'link' => $link,
            'image_url' => $this->fetchImage($node),
            'companies' => [],
        ];
    }

    public function prepareContext()
    {
        $options = [
            'http' => [
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                            "Cookie: y_maps=test\r\n" .
                            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
            ],
        ];

        return stream_context_create($options);
    }

    abstract function fetch($url);

    protected function prepareText($text)
    {
        return $text;
    }

    public function run()
    {
        try
        {
            $this->fetch(static::URL)->filter(static::ITEM_SELECTOR)->each(function(Crawler $node) {
                $meta = $this->buildMeta($node);

                Console::stdout('Статья: ' . $meta['title'] . '... ' . PHP_EOL);

                if ($this->isAlreadyParsed($meta['link']))
                {
                    Console::stdout(' [-] Уже собирали!' . PHP_EOL);

                    return;
                }

                $haystack = mb_strtolower(Common::purify($node->text()));

                $meta['companies'] = Candidates::matches($haystack);

                if (!count($meta['companies']))
                {
                    Console::stdout(' [+] Добавляем без компании' . PHP_EOL);
                }
                else
                {
                    Console::stdout(' [+] Совпало ' . count($meta['companies']) . ' компаний' . PHP_EOL);
                }

                Console::stdout('[+]' . $meta['link'] . PHP_EOL);

                $article_full = $this->fetch($meta['link'])->filter(static::ARTICLE_SELECTOR)->eq(0)->text();

                $meta['text'] = Common::purify($article_full);

                $transaction = \Yii::$app->db->beginTransaction();

                try
                {
                    $is_publish = (int)(count($meta['companies']) > 0);

                    $model = Factory::saveNews($meta['title'], $this->prepareText($meta['text']), $is_publish);

                    Factory::linkCompanies($model, $meta['companies']);


                    if ($image_url = ArrayHelper::getValue($meta, 'image_url'))
                    {
                        Factory::attachImage($model, $image_url);
                    }

                    if (!$image_url && $meta['companies'])
                    {
                        if ($images = Candidates::getImagePlaceholders($meta['companies']))
                        {
                            $image_url = $images[array_rand($images)];
                            Factory::attachImage($model, $image_url);
                        }
                    }

                    Factory::registerJobDone($model, $meta['link'], $meta['pubDate']);

                }
                catch (\Throwable $exception)
                {
                    $transaction->rollBack();
                    throw $exception;
                }

                $transaction->commit();
            });
        } catch (\Throwable $exception)
        {
            Console::output('[<====================== ERROR! ===================>]');
        }
    }

    public function fetchImage(Crawler $node)
    {
        return null;
    }

}