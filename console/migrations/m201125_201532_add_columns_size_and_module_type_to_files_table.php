<?php

use yii\db\Migration;

/**
 * Class m201125_201532_add_columns_size_and_module_type_to_files_table
 */
class m201125_201532_add_columns_size_and_module_type_to_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%files}}', 'size', $this->integer()->unsigned());
        $this->addColumn('{{%files}}', 'module_type', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%files}}', 'size');
        $this->dropColumn('{{%files}}', 'module_type');
    }
}
