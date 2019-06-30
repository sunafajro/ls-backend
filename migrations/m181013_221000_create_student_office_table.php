<?php

use yii\db\Migration;

/**
 * Handles the creation of table `student_office`.
 */
class m181013_221000_create_student_office_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('student_office', [
            'id' => $this->integer()->unsigned()->notNull(),
            'student_id' => $this->integer()->unsigned()->notNull(),
            'office_id' => $this->integer()->unsigned()->notNull(),
            'is_main' => $this->boolean()->notNull()->defaultValue(false),
        ]);

        $this->addPrimaryKey('PRIMARY', 'student_office', 'id');

        $this->createIndex(
            'idx-student_office-student_id',
            'student_office',
            'student_id'
        );

        $this->addForeignKey(
            'fk-student_office-student_id',
            'student_office',
            'student_id',
            'calc_studname',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-student_office-office_id',
            'student_office',
            'office_id'
        );

        $this->addForeignKey(
            'fk-student_office-office_id',
            'student_office',
            'office_id',
            'calc_office',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('student_office');
    }
}
