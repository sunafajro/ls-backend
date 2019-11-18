<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `calc_salestud`.
 */
class m191116_083146_add_reason_column_to_calc_salestud_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('calc_salestud', 'reason', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('calc_salestud', 'reason');
    }
}
