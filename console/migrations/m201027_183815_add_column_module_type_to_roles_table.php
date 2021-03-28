<?php

use yii\db\Migration;

/**
 * Class m201027_183815_add_column_module_type_to_roles_table
 */
class m201027_183815_add_column_module_type_to_roles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%roles}}', 'module_type', $this->string()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%roles}}', 'module_type');
    }
}
