<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m190330_081844_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'site' => $this->integer(4)->notNull()->defaultValue(0),
            'visible' => $this->integer(4)->notNull()->defaultValue(1),
            'login' => $this->string(255)->notNull(),
            'pass' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'status' => $this->integer()->notNull(),
            'calc_teacher' => $this->integer()->notNull(),
            'calc_office' => $this->integer(),
            'calc_city' => $this->integer(),
            'logo' => $this->string(255),
        ]);
        $this->createIndex(
            'idx-user-calc_teacher',
            'user',
            'calc_teacher'
        );
        $this->createIndex(
            'idx-user-calc_office',
            'user',
            'calc_office'
        );
        $this->createIndex(
            'idx-user-calc_city',
            'user',
            'calc_city'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
