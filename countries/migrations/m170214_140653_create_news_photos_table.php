<?php
use yii\db\Schema;
use yii\db\Migration;

class m170214_140653_create_news_photos_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%news_photo}}', [
            'id' => Schema::TYPE_PK,
            'news_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'file' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('idx_news_photo_news_id', '{{%news_photo}}', 'news_id');

        $this->addForeignKey('fk_news_photo_news', '{{%news_photo}}', 'news_id', '{{%news}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%news_photo}}');
    }
}
