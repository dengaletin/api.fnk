<?php

use yii\db\Migration;

class m160401_160612_create_purchase_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%purchase}}', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer(),
            'product_id' => $this->integer()->notNull(),
            'receipt' => $this->text(),
            'created_at' => $this->integer()->notNull(),
            'expired_at' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx_purchase_device_id', '{{%purchase}}', 'device_id');
        $this->createIndex('idx_purchase_product_id', '{{%purchase}}', 'product_id');
        $this->createIndex('idx_purchase_expired_at', '{{%purchase}}', 'expired_at');

        $this->addForeignKey('fk_purchase_device', '{{%purchase}}', 'device_id', '{{%device}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk_purchase_product', '{{%purchase}}', 'product_id', '{{%product}}', 'id', 'CASCADE', 'RESTRICT');

        $this->execute('
            INSERT INTO {{%purchase}} (
                device_id,
                product_id,
                receipt,
                created_at,
                expired_at
            )
            SELECT
                d.id,
                1,
                d.receipt,
                ' . time() . ',
                NULL
            FROM {{%device}} d
            WHERE d.receipt IS NOT NULL
            ORDER BY d.id ASC
        ');

        $this->dropColumn('{{%device}}', 'receipt');
        $this->dropColumn('{{%device}}', 'purchase');
    }

    public function down()
    {
        $this->addColumn('{{%device}}', 'receipt', $this->text());
        $this->addColumn('{{%device}}', 'purchase', $this->smallInteger());

        $this->execute('UPDATE {{%device}} d SET receipt = (
            SELECT MAX(p.receipt) FROM {{%purchase}} p WHERE p.device_id = d.id GROUP BY d.id
        )');

        $this->execute('UPDATE {{%device}} d SET purchase = (
            SELECT 1 FROM {{%purchase}} p WHERE p.device_id = d.id AND d.receipt IS NOT NULL AND (p.expired_at IS NULL OR p.expired_at > :now) GROUP BY d.id
        )', [':now' => time()]);

        $this->dropTable('{{%purchase}}');
    }
}
