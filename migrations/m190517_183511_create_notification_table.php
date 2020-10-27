<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notifications`.
 */
class m190517_183511_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('notifications', [
            'id'         => $this->primaryKey(),
            'visible'    => $this->integer(4)->defaultValue(1)->notNull(),
            'created_at' => $this->date()->notNull()->defaultValue(date('Y-m-d')),
            'count'      => $this->integer()->defaultValue(0)->notNull(),
            'type'       => $this->string()->notNull(),
            'status'     => $this->string()->defaultValue('queue')->notNull(),
            'entity_id'  => $this->integer()->notNull(),
            'user_id'    => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'notifications-entity_id-idx',
            'notifications',
            'entity_id'
        );
        $this->createIndex(
            'notifications-user_id-idx',
            'notifications',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('notifications');
    }
}
