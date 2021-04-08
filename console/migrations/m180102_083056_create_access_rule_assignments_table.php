<?php

use yii\db\Migration;

/**
 * Handles the creation of table `access_rule_assignments`.
 */
class m180102_083056_create_access_rule_assignments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%access_rule_assignments}}', [
            'id'               => $this->primaryKey(),
            'access_rule_slug' => $this->string()->notNull(),
            'role_id'          => $this->integer(),
            'user_id'          => $this->integer(),
        ]);

        $this->createIndex('access_rule_assignments-access_rule_slug-idx', '{{%access_rule_assignments}}', 'access_rule_slug');
        $this->createIndex('access_rule_assignments-role_id-idx', '{{%access_rule_assignments}}', 'role_id');
        $this->createIndex('access_rule_assignments-user_id-idx', '{{%access_rule_assignments}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_rule_assignments}}');
    }
}
