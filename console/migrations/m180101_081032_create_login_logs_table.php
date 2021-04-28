<?php

use yii\db\Migration;

/**
 * Handles the creation of table `login_logs`.
 */
class m180101_081032_create_login_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%login_logs}}', [
            'id'          => $this->primaryKey(),
            'date'        => $this->datetime()->notNull(),
            'result'      => $this->integer()->notNull(),
            'user_id'     => $this->integer()->notNull(),
            'ipaddr'      => $this->string(),
            'module_type' => $this->string()->notNull(),
        ]);

        $this->createIndex('login_logs-user_id-idx', '{{%login_logs}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%login_logs}}');
    }
}
