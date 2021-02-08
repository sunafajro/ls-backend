<?php

use yii\db\Migration;

/**
 * Class m180101_000020_create_login_log_table
 */
class m180101_000020_create_login_log_table extends Migration
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

        $this->createTable('{{%login_log}}', [
            'id' => $this->primaryKey(),
            'date' => $this->dateTime(),
            'result' => $this->tinyInteger(),
            'user_id' => $this->integer(),
            'ip_address' => $this->string(),
            'module_type' => $this->string(),
        ], $tableOptions);

        $this->createIndex('idx-login_log-user_id', '{{%login_log}}', 'user_id');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%login_log}}');

        return true;
    }
}