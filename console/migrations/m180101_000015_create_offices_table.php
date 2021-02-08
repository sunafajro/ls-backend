<?php

use yii\db\Migration;

/**
 * Class m180101_000015_create_offices_table
 */
class m180101_000015_create_offices_table extends Migration
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

        $this->createTable('{{%offices}}', [
            'id'      => $this->primaryKey(),
            'name'    => $this->string(),
            'visible' => $this->tinyInteger(),
            'num'     => $this->integer(),
            'city_id' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx-offices-city_id', '{{%offices}}', 'user_id');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%offices}}');

        return true;
    }
}