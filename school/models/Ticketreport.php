<?php

namespace school\models;

use Yii;

/**
 * This is the model class for table "calc_ticket_report".
 *
 * @property integer $id
 * @property string $data
 * @property integer $calc_ticket
 * @property integer $user
 * @property integer $calc_ticketstatus
 * @property string $comment
 */
class Ticketreport extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_ticket_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user'], 'required'],
            [['comment'], 'string'],
            [['calc_ticket', 'user', 'calc_ticketstatus'], 'integer'],
            [['data'],'date','format'=>'yyyy-mm-dd'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'data' => Yii::t('app', 'Date'),
            'calc_ticket' => Yii::t('app', 'Calc Ticket'),
            'user' => Yii::t('app', 'User'),
            'calc_ticketstatus' => Yii::t('app', 'State'),
            'comment' => Yii::t('app', 'Comment'),
        ];
    }
}
