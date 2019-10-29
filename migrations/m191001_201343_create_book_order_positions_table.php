<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_order_positions}}`.
 */
class m191001_201343_create_book_order_positions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_order_positions}}', [
            'id'               => $this->primaryKey(),
            'book_order_id'    => $this->integer(),
            'book_id'          => $this->integer(),
            'purchase_cost_id' => $this->integer(),
            'selling_cost_id'  => $this->integer(),
            'count'            => $this->integer(),
            'paid'             => 'decimal(10,2)',
            'office_id'        => $this->integer(),
            'user_id'          => $this->integer(),
            'created_at'       => $this->date(),
            'visible'          => $this->tinyInteger(),
        ]);

        $this->createIndex('book_order_positions-book_order_id-idx', 'book_order_positions', 'book_order_id');
        $this->createIndex('book_order_positions-book_id-idx', 'book_order_positions', 'book_id');
        $this->createIndex('book_order_positions-purchase_cost_id-idx', 'book_order_positions', 'purchase_cost_id');
        $this->createIndex('book_order_positions-selling_cost_id-idx', 'book_order_positions', 'selling_cost_id');
        $this->createIndex('book_order_positions-office_id-idx', 'book_order_positions', 'office_id');
        $this->createIndex('book_order_positions-user_id-idx', 'book_order_positions', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_order_positions}}');
    }
}
