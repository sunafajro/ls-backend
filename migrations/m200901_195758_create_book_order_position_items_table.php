<?php

use yii\db\Migration;

/**
 * Handles adding columns to the table `{{%book_order_position_items}}`.
 */
class m200901_195758_create_book_order_position_items_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_order_position_items}}', [
            'id'                     => $this->primaryKey(),
            'book_order_position_id' => $this->integer(),
            'student_name'           => $this->string(),
            'student_id'             => $this->integer(),
            'count'                  => $this->integer(),
            'paid'                   => 'decimal(10,2)',
            'payment_type'           => $this->string(),
            'payment_comment'        => $this->string(),
            'user_id'                => $this->integer(),
            'created_at'             => $this->date(),
            'visible'                => $this->integer(),
        ]);

        $this->createIndex('book_order_position_items-book_order_position_id-idx', '{{%book_order_position_items}}', 'book_order_position_id');
        $this->createIndex('book_order_position_items-student_id-idx', '{{%book_order_position_items}}', 'student_id');
        $this->createIndex('book_order_position_items-user_id-idx', '{{%book_order_position_items}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_order_position_items}}');
    }
}
