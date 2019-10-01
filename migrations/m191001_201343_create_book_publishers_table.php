<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_publishers}}`.
 */
class m191001_201343_create_book_publishers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_publishers}}', [
            'id'      => $this->primaryKey(),
            'name'    => $this->string(),
            'visible' => $this->tinyInteger(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book_publishers}}');
    }
}
