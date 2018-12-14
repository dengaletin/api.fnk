<?php

namespace app\components\parser\helpers;


use app\models\News;
use app\models\NewsPhoto;
use app\models\ParserJobs;
use yii\helpers\FileHelper;
use yii\web\ServerErrorHttpException;

class Factory
{
    public static function saveNews($title, $text, $publish)
    {
        $model = new News([
            'title' => $title,
            'text' => $text,
            'publish' => $publish,
            'date' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
        if (!$model->save()) {
            throw new ServerErrorHttpException('Can\'t save model', 1);
        }

        return $model;
    }

    public static function linkCompanies($model, $company_ids)
    {
        foreach ($company_ids as $company_id) {
            \Yii::$app->db->createCommand()->insert('{{%news_companies}}', [
                'news_id' => $model->id,
                'company_id' => $company_id,
            ])->execute();
        }
    }

    public static function attachImage($model, $url)
    {
        $m = new NewsPhoto([
            'news_id' => $model->id,
            'file' => (md5($url) . '.' . pathinfo($url, PATHINFO_EXTENSION)),
        ]);

        $file = $m->resolvePath($m->filePath);


        FileHelper::createDirectory(dirname($file));

        file_put_contents($file,file_get_contents($url));

        if (!$m->save()) {
            throw new ServerErrorHttpException('Can\'t save model', 2);
        }

        $m->createThumbs();
    }

    public static function registerJobDone($model, $link, $date)
    {
        $model = new ParserJobs([
            'source_url' => $link,
            'article_id' => $model->id,
            'post_time' => $date,
        ]);

        if (!$model->save()) {
            throw new ServerErrorHttpException('Can\'t save model', 3);
        }
    }
}