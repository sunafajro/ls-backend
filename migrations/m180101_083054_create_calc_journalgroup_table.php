<?php

use yii\db\Migration;

/**
 * Handles the creation of table `calc_journalgroup`.
 */
class m180101_083054_create_calc_journalgroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('calc_journalgroup', [
            'id'                => $this->primaryKey(),
            'calc_groupteacher' => $this->integer(),
            'calc_teacher'      => $this->integer(),
            'calc_accrual'      => $this->integer(),
            'calc_edutime'      => $this->integer(),
            'description'       => $this->text(),
            'homework'          => $this->text(),
            'data'              => $this->date(),
            'user'              => $this->integer(),
            // факт редактирования состава занятия менеджером
            'edit'              => $this->tinyInteger(),
            'data_edit'         => $this->date(),
            'user_edit'         => $this->integer(),
            // факт проверки состава занятия менеджером
            'view'              => $this->tinyInteger(),
            'data_view'         => $this->date(),
            'user_view'         => $this->integer(),
            // факт начисления на занятие
            'done'              => $this->tinyInteger(),
            'data_done'         => $this->date(),
            'user_done'         => $this->integer(),
            // факт удаления занятия
            'visible'           => $this->tinyInteger(),
            'data_visible'      => $this->date(),
            'user_visible'      => $this->integer(),
            // непонятно для чего, вероятно нет необходимости
            'audit'             => $this->tinyInteger(),
            'data_audit'        => $this->date(),
            'user_audit'        => $this->integer(),
            'description_audit' => $this->text(),
        ]);

        $this->createIndex('calc_journalgroup-calc_groupteacher-idx', 'calc_journalgroup', 'calc_groupteacher');
        $this->createIndex('calc_journalgroup-calc_teacher-idx', 'calc_journalgroup', 'calc_teacher');
        $this->createIndex('calc_journalgroup-calc_accrual-idx', 'calc_journalgroup', 'calc_accrual');
        $this->createIndex('calc_journalgroup-calc_edutime-idx', 'calc_journalgroup', 'calc_edutime');
        $this->createIndex('calc_journalgroup-user-idx', 'calc_journalgroup', 'user');

        $this->addForeignKey('fk-calc_journalgroup-calc_groupteacher', 'calc_journalgroup', 'calc_groupteacher', 'calc_groupteacher', 'id');
        $this->addForeignKey('fk-calc_journalgroup-calc_teacher', 'calc_journalgroup', 'calc_teacher', 'calc_teacher', 'id');
        // $this->addForeignKey('fk-calc_journalgroup-calc_accrual', 'calc_journalgroup', 'calc_accrual', 'calc_accrualteacher', 'id');
        $this->addForeignKey('fk-calc_journalgroup-user', 'calc_journalgroup', 'user', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('calc_journalgroup');
    }
}
