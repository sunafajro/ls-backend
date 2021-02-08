<?php

use yii\db\Migration;

/**
 * Class m180101_000005_create_roles_table
 */
class m180101_000005_create_roles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): bool
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%roles}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'description' => $this->string(),
            'visible' => $this->tinyInteger(),
        ], $tableOptions);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%roles}}');

        return true;
    }
}