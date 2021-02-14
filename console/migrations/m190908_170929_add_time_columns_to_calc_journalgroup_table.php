<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%calc_journalgroup}}`.
 */
class m190908_170929_add_time_columns_to_calc_journalgroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('calc_journalgroup', 'time_begin', $this->string(5)->defaultValue('00:00'));
        $this->addColumn('calc_journalgroup', 'time_end', $this->string(5)->defaultValue('00:00'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('calc_journalgroup', 'time_begin');
        $this->dropColumn('calc_journalgroup', 'time_end');
    }
}
