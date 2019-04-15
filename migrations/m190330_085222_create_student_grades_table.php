<?php

use yii\db\Migration;

/**
 * Handles the creation of table `student_grades`.
 */
class m190330_085222_create_student_grades_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('student_grades', [
            'id' => $this->primaryKey(),
            'visible' => $this->integer(4)->notNull()->defaultValue(1),
            'date' => $this->date()->notNull()->defaultValue(date('Y-m-d')),
            'user' => $this->integer()->notNull(),
            'score' => $this->string(255),
            'type' => $this->integer(4)->notNull()->defaultValue(0),
            'description' => $this->string(255),
            'contents' => $this->json(),
            'calc_studname' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-student_grades-user',
            'student_grades',
            'user'
        );

        $this->createIndex(
            'idx-student_grades-calc_studname',
            'student_grades',
            'calc_studname'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('student_grades');
    }
}
