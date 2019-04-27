<?php

use yii\db\Migration;

/**
 * Handles the creation of table `receipts`.
 */
class m190423_204707_create_receipts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('receipts', [
            'id' => $this->primaryKey(),
            'visible' => $this->integer(4)->defaultValue(1)->notNull(),
            'date' => $this->date()->notNull(),
            'user' => $this->integer()->notNull(),
            'studentId' => $this->integer()->notNull(),
            'purpose' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'sum' => $this->integer()->notNull(),
            'qrdata' => $this->text()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('receipts');
    }
}
