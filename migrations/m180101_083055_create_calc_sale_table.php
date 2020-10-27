<?php

use yii\db\Migration;

/**
 * Handles the creation of table `calc_sale`.
 */
class m180101_083055_create_calc_sale_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%calc_sale}}', [
            'id'      => $this->primaryKey(),
            'name'    => $this->string(),
            'visible' => $this->tinyInteger(),
            'procent' => $this->tinyInteger(),
            'value'   => $this->float(),
            'base'    => $this->integer(),
            'data'    => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%calc_sale}}');
    }
}
