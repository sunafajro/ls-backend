<?php

use yii\db\Migration;

/**
 * Handles the creation of table office_books.
 */
class m200115_190520_create_office_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('office_books', [
            'id'         => $this->primaryKey(),
            'book_id'    => $this->integer(),
            'office_id'  => $this->integer()->unsigned(),
            'year'       => $this->integer(),    
            'status'     => $this->string(),
            'comment'    => $this->string(),
            'visible'    => $this->tinyInteger(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
        ]);

        $this->createIndex('office_books-book_id-idx', 'office_books', 'book_id');
        $this->createIndex('office_books-office_id-idx', 'office_books', 'office_id');
        $this->createIndex('office_books-user_id-idx', 'office_books', 'user_id');

        $this->addForeignKey(
            'fk-office_books-book_id',
            'office_books',
            'book_id',
            'books',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-office_books-office_id',
            'office_books',
            'office_id',
            'calc_office',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-office_books-user_id',
            'office_books',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('office_books');
    }
}
