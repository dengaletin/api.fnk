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
        //\Yii::$app->db->charset = 'utf8mb4'; // Todo: Внести в Dockerfile
        foreach ($actions as $action) {
            $this->runAction($action);
        }
    }

    public function actionCleanNews()
    {
        $target_date = date('Y-m-d', time() - 3 * 24 * 60 * 60);

//        $command = \Yii::$app->db->createCommand();
//        $command->delete(News::tableName(), ['AND', ['<', 'date', $target_date], ['publish' => '0']]);
//        var_dump($command->getRawSql());

        $num = News::deleteAll(['AND', ['<', 'date', $target_date], ['publish' => '0']]);
        Console::output(sprintf('Удалено %s записей[target_date: %s]', $num, $target_date) . PHP_EOL);
    }
}