<?php

use yii\db\Migration;

/**
 * Handles adding payer to table `{{%receipts}}`.
 */
class m191007_190217_add_payer_column_to_receipts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('receipts', 'user', 'user_id');
        $this->renameColumn('receipts', 'date', 'created_at');
        $this->renameColumn('receipts', 'studentId', 'student_id');
        $this->addColumn('receipts', 'payer', $this->string());
        
        $this->createIndex('receipts-user-idx', 'receipts', 'user_id');
        $this->createIndex('receipts-student_id-idx', 'receipts', 'student_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
