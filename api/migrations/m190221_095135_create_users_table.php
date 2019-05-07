<?php

use yii\db\Migration;

class m190221_095135_create_users_table extends Migration
{
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(128)->notNull(),
            'last_name' => $this->string(128)->notNull(),
            'email' => $this->string(255)->unique()->defaultValue(NULL),
            'email_code' => $this->string(32),
            'email_new' => $this->string(255),
            'phone' => $this->string(20)->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'updated_at' => $this->timestamp()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
