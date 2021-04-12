<?php

use school\models\Notification;
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
        $this->createTable('{{%notifications}}', [
            'id'         => $this->primaryKey(),
            'visible'    => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->date()->notNull(),
            'count'      => $this->integer()->notNull()->defaultValue(0),
            'type'       => $this->string()->notNull(),
            'status'     => $this->string()->notNull()->defaultValue(Notification::STATUS_QUEUE),
            'entity_id'  => $this->integer()->notNull(),
            'user_id'    => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'notifications-entity_id-idx',
            '{{%notifications}}',
            'entity_id'
        );
        $this->createIndex(
            'notifications-user_id-idx',
            '{{%notifications}}',
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
