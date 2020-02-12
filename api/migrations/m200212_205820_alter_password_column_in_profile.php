<?php

use yii\db\Migration;

/**
 * Class m200212_205820_alter_password_column_in_profile
 */
class m200212_205820_alter_password_column_in_profile extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%profile}}', 'password', $this->text()->defaultValue(null));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%profile}}', 'password', $this->text()->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200212_205820_alter_password_column_in_profile cannot be reverted.\n";

        return false;
    }
    */
}
