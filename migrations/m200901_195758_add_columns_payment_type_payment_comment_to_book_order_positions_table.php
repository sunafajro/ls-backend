<?php

use yii\db\Migration;

/**
 * Handles adding columns to the table `{{%book_order_positions}}`.
 */
class m200901_195758_add_columns_payment_type_payment_comment_to_book_order_positions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%book_order_positions}}', 'payment_type', $this->string());
        $this->addColumn('{{%book_order_positions}}', 'payment_comment', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%book_order_positions}}', 'payment_type');
        $this->dropColumn('{{%book_order_positions}}', 'payment_comment');
    }
}
