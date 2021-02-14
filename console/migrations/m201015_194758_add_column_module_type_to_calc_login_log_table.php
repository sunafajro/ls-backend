<?php

use yii\db\Migration;

/**
 * Class m201015_194758_add_column_module_type_to_calc_login_log_table
 */
class m201015_194758_add_column_module_type_to_calc_login_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%calc_login_log}}', 'module_type', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%calc_login_log}}', 'module_type');
    }
}
