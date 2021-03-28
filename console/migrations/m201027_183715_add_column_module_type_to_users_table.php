<?php

use yii\db\Migration;

/**
 * Class m201027_183715_add_column_module_type_to_users_table
 */
class m201027_183715_add_column_module_type_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%users}}', 'module_type', $this->string()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'module_type');
    }
}
