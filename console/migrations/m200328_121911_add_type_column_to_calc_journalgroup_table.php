<?php

use school\models\Journalgroup;
use yii\db\Migration;

/**
 * Handles adding columns to table `calc_journalgroup`.
 */
class m200328_121911_add_type_column_to_calc_journalgroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('calc_journalgroup', 'type', $this->string()->defaultValue(Journalgroup::TYPE_OFFICE));
        $this->alterColumn('calc_journalgroup', 'type', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('calc_journalgroup', 'type');
    }
}
