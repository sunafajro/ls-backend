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
            'id'         => $this->primaryKey(),
            'visible'    => $this->integer(4)->defaultValue(1)->notNull(),
            'created_at' => $this->date()->notNull(),
            'user_id'    => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'purpose'    => $this->string()->notNull(),
            'name'       => $this->string()->notNull(),
            'sum'        => $this->integer()->notNull(),
            'qrdata'     => $this->text()->notNull(),
        ]);

        $this->createIndex(
            'receipts-user_id-idx',
            'receipts',
            'user_id'
        );

        $this->createIndex(
            'receipts-student_id-idx',
            'receipts',
            'student_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('receipts');
    }
}
