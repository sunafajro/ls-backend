<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $id
 * @property integer $visible
 * @property string $created_at
 * @property integer $count
 * @property string $type
 * @property string $status
 * @property integer $payment_id
 * @property integer $user_id
 */

class Notification extends \yii\db\ActiveRecord
{
    const TYPE_PAYMENT   = 'payment';

    const STATUS_FAIL    = 'fail';
    const STATUS_QUEUE   = 'queue';
    const STATUS_SUCCESS = 'success';

    /**
     * @inheritdoc
     */
    public static function tableName() : string
    {
        return 'notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['type', 'payment_id', 'user_id'], 'required'],
            [['count', 'payment_id', 'user_id', 'visible'], 'integer'],
            [['type', 'status'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() : array
    {
        return [
            'id'         => Yii::t('app', 'Id'),
            'visible'    => Yii::t('app', 'Visible'),
            'created_at' => Yii::t('app', 'Created at'),
            'count'      => Yii::t('app', 'Sending count'),
            'type'       => Yii::t('app', 'Notification type'),
            'status'     => Yii::t('app', 'Notification status'),
            'payment_id' => Yii::t('app', 'Payment Id'),
            'user_id'    => Yii::t('app', 'Creator Id'),
        ];
    }

    public static function getStatusLabel(string $key) : string
    {
        $statuses = self::getStatuses();
        return $statuses[$key] ?? '';
    }

    public static function getStatuses() : array
    {
        return [
            self::STATUS_FAIL    => Yii::t('app', 'Fail'),
            self::STATUS_QUEUE   => Yii::t('app', 'Queue'),
            self::STATUS_SUCCESS => Yii::t('app', 'Success'),
        ];
    }
}