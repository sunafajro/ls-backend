<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m180101_083051_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id'           => $this->primaryKey(),
            'site'         => $this->tinyInteger(),
            'visible'      => $this->tinyInteger(),
            'login'        => $this->string(),
            'pass'         => $this->string(),
            'name'         => $this->string(),
            'status'       => $this->integer(),
            'calc_office'  => $this->integer(),
            'calc_teacher' => $this->integer(),
            'calc_city'    => $this->integer(),
            'logo'         => $this->string(),
        ]);

        $this->createIndex('users-calc_office-idx', '{{%users}}', 'calc_office');
        $this->createIndex('users-calc_teacher-idx', '{{%users}}', 'calc_teacher');
        $this->createIndex('users-calc_city-idx', '{{%users}}', 'calc_city');
        $this->createIndex('users-status-idx', '{{%users}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
