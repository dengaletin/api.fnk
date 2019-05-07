<?php

use yii\db\Migration;

class m190304_154922_add_avatar_column_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%users}}', 'avatar', $this->string(64));
    }

    public function down()
    {
        $this->dropColumn('{{%users}}', 'avatar');
    }
}
