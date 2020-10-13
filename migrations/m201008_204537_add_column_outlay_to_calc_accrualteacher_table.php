<?php

use yii\db\Migration;

/**
 * Class m201008_204537_add_column_outlay_to_calc_accrualteacher_table
 */
class m201008_204537_add_column_outlay_to_calc_accrualteacher_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%calc_accrualteacher}}', 'outlay', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%calc_accrualteacher}}', 'outlay');
    }
}
