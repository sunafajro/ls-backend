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
            'visible'    => $this->tinyInteger(),
        ]);

        $this->createIndex('access_rules-role_id-idx', '{{%access_rules}}', 'role_id');
        // $this->addForeignKey('fk-access_rules-role_id', 'access_rules', 'role_id', 'status', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_rules}}');
    }
}
