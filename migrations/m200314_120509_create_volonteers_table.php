<?php

use yii\db\Migration;

/**
 * Handles the creation of table `volonteers`.
 */
class m200314_120509_create_volonteers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('volonteers', [
            'id'         => $this->primaryKey(),
            'name'       => $this->string(),
            'birthdate'  => $this->date(),
            'city'       => $this->string(),
            'occupation' => $this->string(),
            'type'       => $this->string(),
            'social'     => $this->string(),
            'phone'      => $this->string(),
            'note'       => $this->string(),
            'visible'    => $this->tinyInteger(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('volonteers');
    }
}
