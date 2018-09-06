<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news`.
 */
class m170214_140652_create_news_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'text' => $this->text()->notNull(),
            'date' => $this->timestamp()->notNull(),
            'publish' => $this->boolean()->notNull(),
        ], $tableOptions);
        
        $this->createIndex('idx_news_publish', '{{%news}}', 'publish');
        $this->createIndex('idx_news_date', '{{%news}}', 'date');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%news}}');
    }
}
