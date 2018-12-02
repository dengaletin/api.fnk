<?php

use app\models\Company;
use yii\db\Migration;
use yii\helpers\Console;

class m181202_052746_add_parser_column_to_company extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%company}}', 'parser_variations', $this->string());

        $q = Company::find();
        $count = $q->count();
        $i = 0;

        Console::startProgress(0, $count);
        foreach (Company::find()->each() as $company)
        {
            /** @var Company $company */
            $company->updateAttributes([
                'parser_variations' => sprintf('%s%s%s',
                    trim($company->name),
                    Company::VARIATIONS_SEPARATOR,
                    trim($company->name_eng))
            ]);
            Console::updateProgress(++$i, $count);
        }
        Console::endProgress(true);
    }

    public function safeDown()
    {
        $this->dropColumn('{{%company}}', 'parser_variations');
    }
}
