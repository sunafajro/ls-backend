<?php

use yii\db\Migration;

/**
 * Handles the creation of table `roles`.
 */
class m180101_081031_create_roles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%roles}}', [
            'id'           => $this->primaryKey(),
            'visible'      => $this->tinyInteger(),
            'name'         => $this->string(),
            'description'  => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%roles}}');
    }
}
