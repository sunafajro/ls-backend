<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book_providers}}`.
 */
class m191001_201209_create_book_providers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book_providers}}', [
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
        $this->dropTable('{{%book_providers}}');
    }
}
