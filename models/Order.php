<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_orders".
 *
 * @property integer $id
 * @property string $title
 * @property string $number
 * @property string $content
 * @property integer $calc_messwhomtype
 * @property string $date
 * @property integer $user
 * @property integer $approve1
 * @property integer $approve2
 * @property integer $published
 * @property integer $visible
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'number', 'content', 'calc_messwhomtype', 'date', 'user', 'visible'], 'required'],
            [['content'], 'string'],
            [['calc_messwhomtype', 'user', 'approve1', 'approve2', 'published', 'visible'], 'integer'],
            [['date'], 'safe'],
            [['title', 'number'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'number' => Yii::t('app', 'Number'),
            'content' => Yii::t('app', 'Content'),
            'calc_messwhomtype' => Yii::t('app', 'Calc Messwhomtype'),
            'date' => Yii::t('app', 'Date'),
            'user' => Yii::t('app', 'User'),
            'approve1' => Yii::t('app', 'Approve1'),
            'approve2' => Yii::t('app', 'Approve2'),
            'published' => Yii::t('app', 'Published'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }
}
