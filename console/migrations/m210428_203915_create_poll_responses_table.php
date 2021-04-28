<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%poll_responses}}`.
 */
class m210428_203915_create_poll_responses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%poll_responses}}', [
            'id' => $this->primaryKey(),
            'poll_id'    => $this->integer()->notNull(),
            'visible'     => $this->tinyInteger()->notNull()->defaultValue(1),
            'user_id'    => $this->integer()->notNull(),
            'created_at' => $this->datetime()->notNull(),
            'deleted_at'  => $this->datetime(),
        ]);

        $this->createIndex('poll_responses-poll_id-idx', '{{%poll_responses}}', 'poll_id');
        $this->createIndex('poll_responses-user_id-idx', '{{%poll_responses}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%poll_responses}}');
    }
}
