<?php

use yii\db\Migration;

class m180911_201247_create_parser_table extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%parser_jobs}}', [
            'id' => $this->primaryKey(),
            'source_url' => $this->string()->notNull(),
            'source_host' => $this->string()->notNull(),
            'article_id' => $this->integer()->notNull(),
            'company_id' => $this->integer()->notNull(),
            'post_time' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'parse_time' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%parser_jobs}}');
    }
}
