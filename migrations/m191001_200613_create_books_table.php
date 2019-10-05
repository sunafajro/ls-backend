<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%books}}`.
 */
class m191001_200613_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id'                => $this->primaryKey(),
            'name'              => $this->string(),
            'author'            => $this->string(),
            'isbn'              => $this->string(),
            'description'       => $this->text(),
            'publisher'         => $this->string(),
            'language_id'       => $this->integer(),
            'user_id'           => $this->integer(),
            'created_at'        => $this->date(),
            'visible'           => $this->tinyInteger(),
        ]);

        $this->createIndex('books-user_id-idx', 'books', 'user_id');
        $this->createIndex('books-language_id-idx', 'books', 'language_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
