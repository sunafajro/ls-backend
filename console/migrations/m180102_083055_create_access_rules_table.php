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
            'id'          => $this->primaryKey(),
            'slug'        => $this->string()->notNull(),
            'name'        => $this->string()->notNull(),
            'description' => $this->string(),
        ]);

        $this->createIndex('access_rules-slug-idx', '{{%access_rules}}', 'slug', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%access_rules}}');
    }
}
