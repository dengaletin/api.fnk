<?php

namespace app\components\parser;


use app\models\Company;
use app\models\News;
use app\models\ParserJobs;
use Symfony\Component\DomCrawler\Crawler;
use yii\console\Controller;
use yii\db\Expression;

class ParsersController extends Controller
{

    protected function info($message)
    {
        \Yii::info($message, self::className());
    }

    protected function error($message)
    {
        \Yii::error($message, self::className());
    }

    public function beforeAction($action)
    {
        \Yii::$app->getLog()->targets[] =
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['error'],
                'logFile' => '@app/runtime/logs/parser-errors.log',
                'categories' => [self::className()]
            ];
        \Yii::$app->getLog()->targets[] =
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile' => '@app/runtime/logs/parser.log',
                'categories' => [self::className()]
            ];
        return parent::beforeAction($action);
    }

    public function actionRbc()
    {
        (new RbcParser())->run();
    }

    public function actionInterfax()
    {
        (new InterfaxParser())->run();
    }

    public function actionFinam()
    {
        (new FinamParser())->run();
    }
}