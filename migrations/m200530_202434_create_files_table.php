<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%files}}`.
 */
class m200530_202434_create_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(),
            'file_name'     => $this->string(),
            'original_name' => $this->string(),
            'entity_type'   => $this->string(),
            'entity_id'     => $this->integer(),
            'user_id'       => $this->integer(),
            'create_date'   => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%files}}');
    }
}
