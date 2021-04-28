<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%poll_question_responses}}`.
 */
class m210428_203943_create_poll_question_responses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%poll_question_responses}}', [
            'id'               => $this->primaryKey(),
            'poll_id'          => $this->integer()->notNull(),
            'poll_response_id' => $this->integer()->notNull(),
            'poll_question_id' => $this->integer()->notNull(),
            'items'            => $this->json(),
            'visible'          => $this->tinyInteger()->notNull()->defaultValue(1),
            'user_id'          => $this->integer()->notNull(),
            'created_at'       => $this->datetime()->notNull(),
            'deleted_at'       => $this->datetime(),
        ]);

        $this->createIndex('poll_question_responses-poll_id-idx', '{{%poll_question_responses}}', 'poll_id');
        $this->createIndex('poll_question_responses-poll_response_id-idx', '{{%poll_question_responses}}', 'poll_response_id');
        $this->createIndex('poll_question_responses-poll_question_id-idx', '{{%poll_question_responses}}', 'poll_question_id');
        $this->createIndex('poll_question_responses-user_id-idx', '{{%poll_question_responses}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%poll_question_responses}}');
    }
}
