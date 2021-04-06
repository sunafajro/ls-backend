<?php

use yii\db\Migration;

/**
 * Handles the creation of table `access_rules`.
 */
class m180102_083055_create_access_rules_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%access_rules}}', [
            'id'         => $this->primaryKey(),
            'controller' => $this->string(),
            'action'     => $this->string(),
            'role_id'    => $this->integer(),
            'user_id'    => $this->integer(),
        ]);

        $this->createIndex('access_rules-role_id-idx', '{{%access_rules}}', 'role_id');
        $this->createIndex('access_rules-user_id-idx', '{{%access_rules}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_rules}}');
    }
}
