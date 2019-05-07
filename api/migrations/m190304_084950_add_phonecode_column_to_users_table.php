<?php

use yii\db\Migration;

class m190304_084950_add_phonecode_column_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%users}}', 'phone_code', $this->string(32));
    }

    public function down()
    {
        $this->dropColumn('{{%users}}', 'phone_code');
    }
}
