<?php

namespace app\components\parser;


use app\models\Company;
use app\models\News;
use app\models\ParserJobs;
use Symfony\Component\DomCrawler\Crawler;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\Console;

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
                'categories' => [self::LOG_CATEGORY . '.*']
            ];
        \Yii::$app->getLog()->targets[] =
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile' => '@app/runtime/logs/parser.log',
                'categories' => [self::LOG_CATEGORY . '.*']
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
        // Todo: Cron
        $actions = ['rbc', 'interfax', 'finam'];
        \Yii::$app->db->charset = 'utf8mb4'; // Todo: Внести в Dockerfile
        foreach ($actions as $action) {
            $this->runAction($action);
        }
    }

    public function actionCleanNews()
    {
        $target_date = date('Y-m-d', time() - 3 * 24 * 60 * 60);
        $num = News::deleteAll(['<', 'date', $target_date]);
        Console::output(sprintf('Удалено %s записей[target_date: %s]', $num, $target_date) . PHP_EOL);
    }
}