<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%spend_successes}}`.
 */
class m200721_193309_create_spend_successes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%spend_successes}}', [
            'id'         => $this->primaryKey(),
            'visible'    => $this->tinyInteger(),
            'count'      => $this->smallInteger(),
            'cause'      => $this->string(),
            'student_id' => $this->integer(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
        ]);

        $this->createIndex('spend_successes-user_id-idx', 'spend_successes', 'user_id');
        $this->createIndex('spend_successes-student_id-idx', 'spend_successes', 'student_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%spend_successes}}');
    }
}
