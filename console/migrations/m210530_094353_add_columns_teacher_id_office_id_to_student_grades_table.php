<?php

use yii\db\Migration;

/**
 * Class m210530_094353_add_columns_teacher_id_office_id_to_student_grades_table
 */
class m210530_094353_add_columns_teacher_id_office_id_to_student_grades_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%student_grades}}', 'teacher_id', $this->integer());
        $this->addColumn('{{%student_grades}}', 'office_id', $this->integer());

        $this->createIndex('student_grades-teacher_id-idx', '{{%student_grades}}', 'teacher_id');
        $this->createIndex('student_grades-office_id-idx', '{{%student_grades}}', 'office_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%student_grades}}', 'teacher_id');
        $this->dropColumn('{{%student_grades}}', 'office_id');
    }
}
