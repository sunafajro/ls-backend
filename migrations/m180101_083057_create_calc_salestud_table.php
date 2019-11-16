<?php

use yii\db\Migration;

/**
 * Handles the creation of table `calc_salestud`.
 */
class m180101_083057_create_calc_salestud_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('calc_salestud', [
            'id'            => $this->primaryKey(),
            'calc_studname' => $this->integer(),
            'calc_sale'     => $this->integer(),
            'user'          => $this->integer(),
            'data'          => $this->date(),
            'visible'       => $this->tinyInteger(),
            'data_visible'  => $this->date(),
            'user_visible'  => $this->integer(),
            'data_used'     => $this->date(),
            'user_used'     => $this->integer(),
            'approved'      => $this->tinyInteger(),
        ]);

        $this->createIndex('calc_salestud-calc_studname-idx', 'calc_salestud', 'calc_studname');
        $this->createIndex('calc_salestud-calc_sale-idx', 'calc_salestud', 'calc_sale');
        $this->createIndex('calc_salestud-user-idx', 'calc_salestud', 'user');
        $this->createIndex('calc_salestud-user_visible-idx', 'calc_salestud', 'user_visible');
        $this->createIndex('calc_salestud-user_used-idx', 'calc_salestud', 'user_used');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('calc_salestud');
    }
}
