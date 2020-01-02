<?php

use yii\db\Migration;

/**
 * Class m200102_162015_add_column_office_id_to_student_commissions_table
 */
class m200102_162015_add_column_office_id_to_student_commissions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_commissions', 'office_id', $this->integer());

        $this->createIndex('student_commissions-office_id-idx', 'student_commissions', 'office_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('student_commissions', 'office_id');
    }
}
