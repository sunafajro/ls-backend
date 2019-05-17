<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification`.
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
            'payment_id' => $this->integer()->notNull(),
            'user_id'    => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-notifications-payment_id',
            'notifications',
            'payment_id'
        );
        $this->createIndex(
            'idx-notifications-user_id',
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
