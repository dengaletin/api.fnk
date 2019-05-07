<?php

use yii\db\Migration;

class m190304_133340_add_bearertoken_column_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%users}}', 'bearer_token', $this->string(128)->unique());
    }

    public function down()
    {
        $this->dropColumn('{{%users}}', 'bearer_token');
    }
}
