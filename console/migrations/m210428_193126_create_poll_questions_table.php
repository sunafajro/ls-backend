<?php

use yii\db\Migration;

/**
 * Class m210428_193126_create_poll_questions_table
 */
class m210428_193126_create_poll_questions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%poll_questions}}', [
            'id'          => $this->primaryKey(),
            'poll_id'     => $this->integer()->notNull(),
            'title'       => $this->string()->notNull(),
            'type'        => $this->string()->notNull(),
            'items'       => $this->json(),
            'visible'     => $this->tinyInteger()->notNull()->defaultValue(1),
            'user_id'     => $this->integer()->notNull(),
            'created_at'  => $this->datetime()->notNull(),
            'deleted_at'  => $this->datetime(),
        ]);

        $this->createIndex('poll_questions-poll_id-idx', '{{%poll_questions}}', 'poll_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%poll_questions}}');
    }
}
