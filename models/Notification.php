<?php

namespace app\models;

use Yii;
use app\models\Moneystud;
use app\models\Student;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $id
 * @property integer $visible
 * @property string $created_at
 * @property integer $count
 * @property string $type
 * @property string $status
 * @property integer $entity_id
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
            [['type', 'entity_id', 'user_id'], 'required'],
            [['count', 'entity_id', 'user_id', 'visible'], 'integer'],
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
            'entity_id'  => Yii::t('app', 'Entity Id'),
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

    public static function getNotificationSubject(string $type) : string
    {
        $subject = '';
        switch($type) {
            case self::TYPE_PAYMENT:
                $subject = '[ШИЯ Язык для Успеха] Уведомление об оплате';
                break;
        }
        return $subject;
    }

    public static function getNotificationBody(string $type, string $name = '', string $date = '', string $value = '') : string
    {
        $body = '';
        switch($type) {
            case self::TYPE_PAYMENT:
                $body =  '<p><b>' . $name . '</b>, уведомляем вас о том, что ваш платеж от <b>' . date('d.m.Y', strtotime($date)) . '</b> в размере <b>' . $value . 'р.</b> успешно зачислен на баланс.</p>';
                break;
        }
        return $body;
    }

    public static function getTextColorClassByStatus(string $status = NULL) : string
    {
        $result = '';
        switch($status) {
            case self::STATUS_FAIL:
                $result = 'text-danger';
                break;
            case self::STATUS_QUEUE:
                $result = 'text-info';
                break;
            case self::STATUS_SUCCESS:
                $result = 'text-success';
                break;
        }
        return $result;
    }

    public function getNotificationsByStatus(string $status) : array
    {
        $payments = $this->getPaymentNotificationsByStatus($status);
        return $payments;
    }

    public function getPaymentNotificationsByStatus(string $status) : array
    {
        $notifications = (new \yii\db\Query())
        ->select([
            'notificationId'   => 'n.id',
            'notificationType' => 'n.type',
            'paymentDate'      => 'p.data',
            'paymentValue'     => 'p.value',
            'recipientName'    => 's.name',
            'recipientEmail'   => 's.email', 
        ])
        ->from(['n'      => Notification::tableName()])
        ->innerJoin(['p' => Moneystud::tableName()], 'n.entity_id = p.id')
        ->innerJoin(['s' => Student::tableName()],   'p.calc_studname = s.id')
        ->where([
            'n.status' => $status 
        ])
        ->orderBy(['n.created_at' => SORT_ASC])
        ->all();
        return $notifications;
    }
}
