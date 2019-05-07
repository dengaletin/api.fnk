<?php

use yii\db\Migration;

class m190304_095638_add_phonenew_column_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%users}}', 'phone_new', $this->string(20));
    }

    public function down()
    {
        $this->dropColumn('{{%users}}', 'phone_new');
    }
}
