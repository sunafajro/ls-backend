<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `calc_studname`.
 */
class m191118_184338_add_commission_column_to_calc_studname_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('calc_studname', 'commission', 'decimal(10,2)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('calc_studname', 'commission');
    }
}
