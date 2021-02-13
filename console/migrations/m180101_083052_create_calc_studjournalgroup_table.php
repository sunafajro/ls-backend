<?php

use yii\db\Migration;

/**
 * Handles the creation of table `calc_studjournalgroup`.
 */
class m180101_083052_create_calc_studjournalgroup_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%calc_studjournalgroup}}', [
            'id'                 => $this->primaryKey(),
            'calc_groupteacher'  => $this->integer(),
            'calc_journalgroup'  => $this->integer(),
            'calc_studname'      => $this->integer(),
            'calc_statusjournal' => $this->tinyInteger(),
            'comments'           => $this->string(),
            'data'               => $this->date(),
            'user'               => $this->integer(),
        ]);

        $this->createIndex('calc_journalgroup-calc_groupteacher-idx', '{{%calc_studjournalgroup}}', 'calc_groupteacher');
        $this->createIndex('calc_journalgroup-calc_journalgroup-idx', '{{%calc_studjournalgroup}}', 'calc_journalgroup');
        $this->createIndex('calc_journalgroup-calc_studname-idx', '{{%calc_studjournalgroup}}', 'calc_studname');
        $this->createIndex('calc_journalgroup-calc_statusjournal-idx', '{{%calc_studjournalgroup}}', 'calc_statusjournal');
        $this->createIndex('calc_journalgroup-user-idx', '{{%calc_studjournalgroup}}', 'user');

        // $this->addForeignKey('fk-calc_studjournalgroup-calc_groupteacher', 'calc_studjournalgroup', 'calc_groupteacher', 'calc_groupteacher', 'id');
        // $this->addForeignKey('fk-calc_studjournalgroup-calc_journalgroup', 'calc_studjournalgroup', 'calc_journalgroup', 'calc_journalgroup', 'id');
        // $this->addForeignKey('fk-calc_studjournalgroup-calc_studname', 'calc_studjournalgroup', 'calc_studname', 'calc_studname', 'id');
        // $this->addForeignKey('fk-calc_studjournalgroup-user', 'calc_studjournalgroup', 'user', 'user', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%calc_journalgroup}}');
    }
}
