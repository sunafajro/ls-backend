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
        $this->addColumn('receipts', 'payer', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('receipts', 'payer');
    }
}
