<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users_time_tracking}}`.
 */
class m201212_135300_create_users_time_tracking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users_time_tracking}}', [
            'id'         => $this->primaryKey(),
            'entity_id'  => $this->integer(),
            'type'       => $this->string(),
            'start'      => $this->timestamp(),
            'end'        => $this->timestamp(),
            'comment'    => $this->string(),
            'visible'    => $this->tinyInteger(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
        ]);

        $this->createIndex('users_time_tracking-entity_id-idx', '{{%users_time_tracking}}', 'entity_id');
        $this->createIndex('users_time_tracking-user_id-idx', '{{%users_time_tracking}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users_time_tracking}}');
    }
}
