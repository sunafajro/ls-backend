<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_work_hours}}`.
 */
class m201212_135300_create_user_work_hours_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_work_hours}}', [
            'id'         => $this->primaryKey(),
            'entity_id'  => $this->integer(),
            'type'       => $this->string(),
            'date_start' => $this->date(),
            'date_end'   => $this->date(),
            'time_start' => $this->time(),
            'time_end'   => $this->time(),
            'comment'    => $this->string(),
            'visible'    => $this->tinyInteger(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_work_hours}}');
    }
}
