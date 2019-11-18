<?php

use yii\db\Migration;

/**
 * Handles the creation of table `student_commissions`.
 */
class m191118_175054_create_student_commissions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('student_commissions', [
            'id'         => $this->primaryKey(),
            'student_id' => $this->integer(),
            'date'       => $this->date(),
            'debt'       => 'decimal(10,2)',
            'percent'    => 'decimal(5,2)',
            'value'      => 'decimal(10,2)',
            'comment'    => $this->string(),
            'visible'    => $this->tinyInteger(),
            'user_id'    => $this->integer(),
            'created_at' => $this->date(),
        ]);

        $this->createIndex('student_commissions-student_id-idx', 'student_commissions', 'student_id');
        $this->createIndex('student_commissions-user_id-idx', 'student_commissions', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('student_commissions');
    }
}
