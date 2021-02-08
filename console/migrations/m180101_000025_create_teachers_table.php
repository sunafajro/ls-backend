<?php

use yii\db\Migration;

/**
 * Class m180101_000025_create_teachers_table
 */
class m180101_000025_create_teachers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): bool
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teachers}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'birthdate' => $this->date(),
            'phone' => $this->string(),
            'address' => $this->string(),
            'social_link' => $this->string(),
            'email' => $this->string(),
            'value_corp' => $this->float(),
            'accrual' => $this->float(),
            'fund' => $this->float(),
            'description' => $this->string(),
            'employment_type' => $this->tinyInteger(),
            'old' => $this->tinyInteger(),
            'visible' => $this->tinyInteger(),
        ], $tableOptions);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%teachers}}');

        return true;
    }
}