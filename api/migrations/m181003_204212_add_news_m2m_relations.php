<?php

use yii\db\Migration;

class m181003_204212_add_news_m2m_relations extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%news_companies}}', [
            'id' => $this->primaryKey(),
            'news_id' => $this->integer(),
            'company_id' => $this->integer()
        ], $tableOptions);

        $this->dropColumn('{{%parser_jobs}}', 'company_id');
    }

    public function down()
    {
        $this->dropTable('{{%news_companies}}');
        $this->addColumn('{{%parser_jibs}}', 'company_id', $this->integer()->notNull());
    }
}
