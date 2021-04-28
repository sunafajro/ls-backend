<?php

use yii\db\Migration;

/**
 * Class m210428_193110_create_polls_table
 */
class m210428_193110_create_polls_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%polls}}', [
            'id'          => $this->primaryKey(),
            'entity_type' => $this->string()->notNull(),
            'active'      => $this->tinyInteger()->notNull()->defaultValue(0),
            'title'       => $this->string()->notNull(),
            'visible'     => $this->tinyInteger()->notNull()->defaultValue(1),
            'user_id'     => $this->integer()->notNull(),
            'created_at'  => $this->datetime()->notNull(),
            'deleted_at'  => $this->datetime(),
        ]);

        $this->createIndex('polls-user_id-idx', '{{%polls}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%polls}}');
    }
}
