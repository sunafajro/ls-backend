<?php

use yii\db\Migration;

/**
 * Handles the creation of table `language_premiums`.
 */
class m190409_215300_create_language_premiums_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('language_premiums', [
            'id' => $this->primaryKey(),
            'created_at' => $this->date()->notNull()->defaultValue(date('Y-m-d')),    
            'language_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'value' => $this->integer()->notNull(),
            'visible' => $this->integer(4)->notNull()->defaultValue(1),
        ]);
        $this->createIndex(
            'language_premiums-language_id-idx',
            'language_premiums',
            'language_id'
        );
        $this->createIndex(
            'language_premiums-user_id-idx',
            'language_premiums',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('language_premiums');
    }
}
