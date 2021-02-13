<?php

use yii\db\Migration;

/**
 * Handles adding columns to table calc_studname.
 */
class m200219_172951_add_setting_column_to_calc_studname_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('calc_studname', 'settings', 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('calc_studname', 'settings');
    }
}
