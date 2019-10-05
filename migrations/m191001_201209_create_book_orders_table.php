<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_orders}}`.
 */
class m191001_201209_create_book_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_orders}}', [
            'id'            => $this->primaryKey(),
            'date_start'    => $this->date(),
            'date_end'      => $this->date(),
            'count'         => $this->integer(),
            'purchase_cost' => 'decimal(10,2)',
            'selling_cost'  => 'decimal(10,2)',
            'status'        => $this->string(),
            'user_id'       => $this->integer(),
            'created_at'    => $this->date(),
            'visible'       => $this->tinyInteger(),
        ]);

        $this->createIndex('book_orders-user_id-idx', 'book_orders', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_orders}}');
    }
}
