<?php

namespace client\models;

use Yii;

/**
 * This is the model class for table "calc_message".
 *
 * @property integer $id
 * @property integer $visible
 * @property integer $longmess
 * @property string $name
 * @property string $description
 * @property string $files
 * @property integer $user
 * @property string $data
 * @property integer $send
 * @property integer $calc_messwhomtype
 * @property string $refinement
 * @property integer $refinement_id
 */
class Message extends \yii\db\ActiveRecord
{
    const TARGET_ALL_STUDENTS = 12;
    const TARGET_ONE_STUDENT = 13;
    const TARGET_USER = 100;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'calc_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_messwhomtype'], 'default', 'value' => 100],
            [['send'], 'default', 'value' => 1],
            [['user'], 'default', 'value' => Yii::$app->user->id],
            [['data'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['visible'], 'default', 'value' => 1],
            [['visible', 'longmess', 'user', 'send', 'calc_messwhomtype', 'refinement_id'], 'integer'],
            [['name', 'description', 'files', 'refinement'], 'string'],
            [['data'], 'safe'],
            [['name', 'description', 'user', 'calc_messwhomtype', 'refinement_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visible' => Yii::t('app', 'Visible'),
            'longmess' => Yii::t('app', 'Is long'),
            'name' => Yii::t('app', 'Message subject'),
            'description' => Yii::t('app', 'Message text'),
            'files' => Yii::t('app', 'Files'),
            'user' => Yii::t('app','Sender id'),
            'data' => Yii::t('app', 'Date'),
            'send' => Yii::t('app', 'Is sended'),
            'calc_messwhomtype' => Yii::t('app','Receiver type'),
            'refinement' => Yii::t('app','Receiver name'),
            'refinement_id' => Yii::t('app','Receiver name'),
            
        ];
    }
}