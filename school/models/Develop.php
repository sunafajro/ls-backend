<?php

namespace school\models;

use Yii;

/**
 * This is the model class for table "calc_develop".
 *
 * @property integer $id
 * @property integer $type
 * @property string $creation_date
 * @property integer $creation_user
 * @property string $description
 * @property integer $severity
 * @property integer $status
 * @property string $close_date
 * @property integer $close_user
 * @property integer $visible
 */
class Develop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_develop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'creation_date', 'creation_user', 'severity', 'status', 'visible'], 'required'],
            [['type', 'creation_user', 'severity', 'status', 'close_user', 'visible'], 'integer'],
            [['creation_date', 'close_date'], 'safe'],
            [['description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => Yii::t('app', 'Type'),
            'creation_date' => 'Creation Date',
            'creation_user' => 'Creation User',
            'description' => Yii::t('app', 'Description'),
            'severity' => Yii::t('app', 'Severity'),
            'status' => 'Status',
            'close_date' => 'Close Date',
            'close_user' => 'Close User',
            'visible' => 'Visible',
        ];
    }
}
