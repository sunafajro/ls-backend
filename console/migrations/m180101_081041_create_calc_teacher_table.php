<?php

use yii\db\Migration;

/**
 * Class m180101_081041_create_calc_teacher_table
 */
class m180101_081041_create_calc_teacher_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%calc_teacher}}', [
            'id'             => $this->primaryKey(),
            'visible'        => $this->tinyInteger()->notNull()->defaultValue(1),
            'old'            => $this->tinyInteger()->defaultValue(0),
            'name'           => $this->string()->notNull(),
            'birthdate'      => $this->date(),
            'phone'          => $this->string(),
            'email'          => $this->string(),
            'address'        => $this->string(),
            'social_link'    => $this->string(),
            'value_corp'     => $this->decimal(10, 2),
            'accrual'        => $this->decimal(10, 2),
            'fund'           => $this->decimal(10, 2),
            'description'    => $this->text(),
            'calc_statusjob' => $this->tinyInteger()->notNull(),
            'company'        => $this->tinyInteger()->defaultValue(1),
        ]);

        $this->createIndex('calc_teacher-calc_statusjob-idx', '{{%calc_teacher}}', 'calc_statusjob');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%calc_teacher}}');
    }
}
