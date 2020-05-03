<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calc_studjournalgroup".
 *
 * @property integer $id
 * @property integer $calc_groupteacher
 * @property integer $calc_journalgroup
 * @property integer $calc_studname
 * @property integer $calc_statusjournal
 * @property string  $comments
 * @property string  $data
 * @property integer $user
 */
class Studjournalgroup extends ActiveRecord
{
    use StudentMergeTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studjournalgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data'], 'default', 'value' => date('Y-m-d')],
            [['user'], 'default', 'value' => Yii::$app->user->identity->id ?? 0],
            [['calc_groupteacher', 'calc_journalgroup', 'calc_studname', 'calc_statusjournal', 'user'], 'integer'],
            [['data', 'comments'], 'string'],
            [['calc_groupteacher', 'calc_journalgroup', 'calc_studname', 'calc_statusjournal', 'comments', 'data', 'user'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => Yii::t('app', 'ID'),
            'calc_groupteacher'  => Yii::t('app', 'Group ID'),
            'calc_journalgroup'  => Yii::t('app', 'Lesson ID'),
            'calc_studname'      => Yii::t('app', 'Student ID'),
            'calc_statusjournal' => Yii::t('app', 'Status'),
            'comments'           => Yii::t('app', 'Comments'),
            'data'               => Yii::t('app', 'Date'),
            'user'               => Yii::t('app', 'User ID'),
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
