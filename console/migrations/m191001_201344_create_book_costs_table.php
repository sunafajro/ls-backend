<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_costs}}`.
 */
class m191001_201344_create_book_costs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_costs}}', [
            'id'         => $this->primaryKey(),
            'book_id'    => $this->integer(),
            'cost'       => 'decimal(10,2)',
            'type'       => $this->string(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
            'visible'    => $this->tinyInteger(),
        ]);

        $this->createIndex('book_costs-book_id-idx', 'book_costs', 'book_id');
        $this->createIndex('book_costs-user_id-idx', 'book_costs', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_costs}}');
    }
}
