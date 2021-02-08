<?php

use yii\db\Migration;

/**
 * Class m180101_00001_create_users_table
 */
class m180101_00001_create_users_table extends Migration
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

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'password' => $this->string(),
            'name' => $this->string(),
            'role_id' => $this->integer(),
            'office_id' => $this->integer(),
            'teacher_id' => $this->integer(),
            'city_id' => $this->integer(),
            'logo' => $this->string(),
            'visible' => $this->tinyInteger(),
            'site' => $this->tinyInteger(),
        ], $tableOptions);

        $this->createIndex('idx-users-office_id', '{{%users}}', 'office_id');
        $this->createIndex('idx-users-teacher_id', '{{%users}}', 'teacher_id');
        $this->createIndex('idx_users-city_id', '{{%users}}', 'city_id');
        $this->createIndex('idx_users-role_id', '{{%users}}', 'role_id');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%users}}');

        return true;
    }
}