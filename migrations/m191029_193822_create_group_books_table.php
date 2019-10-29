<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group_books}}`.
 */
class m191029_193822_create_group_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%group_books}}', [
            'id'       => $this->primaryKey(),
            'book_id'  => $this->integer(),
            'group_id' => $this->integer(),
            'primary'  => $this->tinyInteger(),
        ]);

        $this->createIndex('group_books-book_id-idx', 'group_books', 'book_id');
        $this->createIndex('group_books-group_id-idx', 'group_books', 'group_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%group_books}}');
    }
}
