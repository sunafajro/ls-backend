<?php

use yii\db\Migration;

/**
 * Handles the creation of table `teacher_language_premiums`.
 */
class m190409_215400_create_teacher_language_premiums_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('teacher_language_premiums', [
            'id' => $this->primaryKey(),
            'company' => $this->integer(4)->notNull()->defaultValue(1),
            'created_at' => $this->date()->notNull()->defaultValue(date('Y-m-d')),
            'language_premium_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'visible' => $this->integer(4)->notNull()->defaultValue(1),
        ]);
        $this->createIndex(
            'idx-teacher_language_premiums-language_premium_id',
            'teacher_language_premiums',
            'language_premium_id'
        );
        $this->createIndex(
            'idx-teacher_language_premiums-teacher_id',
            'teacher_language_premiums',
            'teacher_id'
        );
        $this->createIndex(
            'idx-teacher_language_premiums-user_id',
            'teacher_language_premiums',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('teacher_language_premiums');
    }
}
