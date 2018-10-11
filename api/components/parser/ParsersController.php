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

    const LOG_CATEGORY = 'NewsParser';

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
                'categories' => [self::LOG_CATEGORY.'.*']
            ];
        \Yii::$app->getLog()->targets[] =
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile' => '@app/runtime/logs/parser.log',
                'categories' => [self::LOG_CATEGORY.'.*']
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

    public function actionAll()
    {
        $actions = ['rbc', 'interfax', 'finam'];
        \Yii::beginProfile('total', self::LOG_CATEGORY.'.profile');
        foreach ($actions as $action)
        {
            \Yii::beginProfile($action, self::LOG_CATEGORY.'.profile');
            $this->runAction($action);
            \Yii::endProfile($action, self::LOG_CATEGORY.'.profile');
        }
        \Yii::endProfile('total', self::LOG_CATEGORY.'.profile');
    }
}