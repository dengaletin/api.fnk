<?php

namespace app\components\parser;


use app\models\Company;
use app\models\News;
use app\models\NewsPhoto;
use app\models\ParserJobs;
use Symfony\Component\DomCrawler\Crawler;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;
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

    private function getCandidates()
    {
        $cols = preg_filter('/^name.*/', '$0', array_keys((new Company())->attributes));
        $cols[] = 'id';
        $companies = Company::find()->select(implode(',', $cols))->asArray()->all();

        $companies = array_map(function ($item) {
            $id = $item['id'];
            unset($item['id']);
            return [
                'id' => $id,
                'preg_condition' => '/[\W]+(' . implode('|', array_map(function ($str) {
                        return preg_quote(mb_strtolower(trim($str)), '/');
                    }, array_filter(array_values($item)))) . ')[\W]+/i'
            ];
        }, $companies);

        return $companies;
    }

    private function isAlreadyParsed($link)
    {
        return (bool)((new \yii\db\Query())
            ->from('{{%parser_jobs}}')
            ->where(['source_url' => $link])
            ->count('id'));
    }

    private function buildMeta(Crawler $node)
    {
        return [
            'title' => trim($node->filter(static::TITLE_SELECTOR)->eq(0)->text()),
            'pubDate' => (new \DateTime(
                $node->filter(static::DATE_SELECTOR)->eq(0)->text(),
                new \DateTimeZone(static::TIMEZONE)))
                ->format('y-m-d H:i:s'),
            'link' => trim($node->filter(static::LINK_SELECTOR)->eq(0)->text()),
            'image_url' => $this->fetchImage($node)
        ];
    }

    abstract function fetch($url);

    protected function prepareText($text)
    {
        return $text;
    }

    public function run()
    {
        $this->fetch(static::URL)
            ->filter(static::ITEM_SELECTOR)
            ->each(function (Crawler $node) {
                $companies = $this->getCandidates();
                $meta = $this->buildMeta($node);
                Console::stdout('Статья: ' . $meta['title'] . '... ' . PHP_EOL);
                foreach ($companies as $company) {

                    if (preg_match($company['preg_condition'], $node->text())) {
                        $meta['companies'][] = $company;
                        Console::stdout(' [!] Найдена компания #' . $company['id'] . PHP_EOL);
                    }
                };

                if ($this->isAlreadyParsed($meta['link'])) {
                    Console::stdout(' [-] Уже собирали!' . PHP_EOL);
                    return;
                }
                if (!isset($meta['companies']) || !count($meta['companies'])) {
                    Console::stdout(' [+] Добавляем без компании' . PHP_EOL);
                    //return;
                } else {
                    Console::stdout(' [+] Совпало ' . count($meta['companies']) . ' компаний' . PHP_EOL);
                    //return;
                }


                Console::stdout('[+]' . $meta['link'] . PHP_EOL);

                $meta['text'] = trim(preg_replace(
                    '/(\s|\n|\t|\r){2,}/',
                    '$1',
                    $this->fetch($meta['link'])
                        ->filter(static::ARTICLE_SELECTOR)
                        ->eq(0)
                        ->text()));

                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    $model = new News([
                        'title' => $meta['title'],
                        'text' => $this->prepareText($meta['text']),
                        'publish' => (int)((isset($meta['companies']) && count($meta['companies']) > 0)),
                        'date' => (new \DateTime())->format('Y-m-d H:i:s'),
                    ]);
                    if (!$model->save()) {
                        throw new ServerErrorHttpException('Can\'t save model', 1);
                    }
                    if (isset($meta['companies'])) {
                        foreach ($meta['companies'] as $company) {
                            \Yii::$app->db->createCommand()->insert('{{%news_companies}}', [
                                'news_id' => $model->id,
                                'company_id' => $company['id'],
                            ])->execute();
                        }
                    }
                    if ($image_url = ArrayHelper::getValue($meta, 'image_url')) {
                        $this->attachImage($image_url, $model->id);
                    }
                    $model = new ParserJobs([
                        'source_url' => $meta['link'],
                        'article_id' => $model->id,
                        'post_time' => $meta['pubDate'],
                    ]);

                    if (!$model->save()) {
                        throw new ServerErrorHttpException('Can\'t save model', 1);
                    }
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


    public function attachImage($url, $news_id)
    {
        $m = new NewsPhoto();
        $m->news_id = $news_id;
        $m->file = md5($url) . '.' . pathinfo($url, PATHINFO_EXTENSION);

        FileHelper::createDirectory(dirname($m->resolvePath($m->filePath)));
        file_put_contents(
            $m->resolvePath($m->filePath),
            file_get_contents($url)
        );

        if ($m->save()) {
            $m->createThumbs();
        }

    }

}