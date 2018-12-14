<?php

namespace app\components\parser;


use app\components\parser\helpers\Candidates;
use app\components\parser\helpers\Common;
use app\components\parser\helpers\Factory;
use app\models\Company;
use app\models\News;
use app\models\NewsPhoto;
use app\models\ParserJobs;
use Symfony\Component\DomCrawler\Crawler;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;
use yii\web\ServerErrorHttpException;

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

    abstract function fetch($url);

    protected function prepareText($text)
    {
        return $text;
    }

    public function run()
    {
        $this->fetch(static::URL)->filter(static::ITEM_SELECTOR)->each(function (Crawler $node) {
            $meta = $this->buildMeta($node);

            Console::stdout('Статья: ' . $meta['title'] . '... ' . PHP_EOL);

            if ($this->isAlreadyParsed($meta['link'])) {
                Console::stdout(' [-] Уже собирали!' . PHP_EOL);
                return;
            }

            $haystack = Common::purify($node->text());

            $meta['companies'] = Candidates::matches($haystack);

            if (!count($meta['companies'])) {
                Console::stdout(' [+] Добавляем без компании' . PHP_EOL);
            } else {
                Console::stdout(' [+] Совпало ' . count($meta['companies']) . ' компаний' . PHP_EOL);
            }

            Console::stdout('[+]' . $meta['link'] . PHP_EOL);

            $article_full = $this->fetch($meta['link'])->filter(static::ARTICLE_SELECTOR)->eq(0)->text();

            $meta['text'] = Common::purify($article_full);

            $transaction = \Yii::$app->db->beginTransaction();

            try {
                $is_publish = (int)(count($meta['companies']) > 0);

                $model = Factory::saveNews($meta['title'], $this->prepareText($meta['text']), $is_publish);

                Factory::linkCompanies($model, $meta['companies']);


                if ($image_url = ArrayHelper::getValue($meta, 'image_url')) {
                    Factory::attachImage($model, $image_url);
                }

                Factory::registerJobDone($model, $meta['link'], $meta['pubDate']);

            } catch (\Throwable $exception) {
                $transaction->rollBack();
                throw $exception;
            }

            $transaction->commit();
        });
    }

    public function fetchImage(Crawler $node)
    {
        return null;
    }

}