<?php

namespace school\models;

use school\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_studjournalgrouphistory".
 *
 * @property integer $id
 * @property integer $calc_groupteacher
 * @property integer $calc_journalgroup
 * @property integer $calc_studname
 * @property integer $calc_statusjournal
 * @property string $comments
 * @property string $data
 * @property integer $user
 * @property integer $timestamp_id
 * @property string $description
 */
class Studjournalgrouphistory extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studjournalgrouphistory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_groupteacher', 'calc_journalgroup', 'calc_studname', 'calc_statusjournal', 'comments', 'data', 'user', 'timestamp_id', 'description'], 'required'],
            [['calc_groupteacher', 'calc_journalgroup', 'calc_studname', 'calc_statusjournal', 'user', 'timestamp_id'], 'integer'],
            [['comments', 'description'], 'string'],
            [['data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_groupteacher' => Yii::t('app', 'Calc Groupteacher'),
            'calc_journalgroup' => Yii::t('app', 'Calc Journalgroup'),
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'calc_statusjournal' => Yii::t('app', 'Calc Statusjournal'),
            'comments' => Yii::t('app', 'Comments'),
            'data' => Yii::t('app', 'Data'),
            'user' => Yii::t('app', 'User'),
            'timestamp_id' => Yii::t('app', 'Timestamp ID'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
    
    /**
     * @deprecated
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * @param integer @id1
     * @param integer @id2
     * @return boolean
     */
    public static function changeStudentId($id1, $id2)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['calc_studname' => $id1], ['calc_studname' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
