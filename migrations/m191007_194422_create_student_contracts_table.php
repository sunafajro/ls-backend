<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_contracts}}`.
 */
class m191007_194422_create_student_contracts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%student_contracts}}', [
            'id'         => $this->primaryKey(),
            'student_id' => $this->integer(),
            'number'     => $this->string(),
            'date'       => $this->date(),
            'signer'     => $this->string(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
            'visible'    => $this->tinyInteger(),
        ]);

        $this->createIndex('student_contracts-student_id-idx', 'student_contracts', 'student_id');
        $this->createIndex('student_contracts-user_id-idx', 'student_contracts', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student_contracts}}');
    }
}
