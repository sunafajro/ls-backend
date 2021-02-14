<?php

use yii\db\Migration;

/**
 * Class m200501_105129_add_column_successes_to_calc_studjournalgroup_table
 */
class m200501_105129_add_column_successes_to_calc_studjournalgroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('calc_studjournalgroup', 'successes', $this->tinyInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('calc_studjournalgroup', 'successes');
    }
}
