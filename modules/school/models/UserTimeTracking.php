<?php


namespace app\modules\school\models;

use app\modules\school\models\queries\UserTimeTrackingQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class UserTimeTracking
 * @package app\modules\school\models
 *
 * @property int    $id
 * @property int    $entity_id
 * @property string $type
 * @property string $start
 * @property string $end
 * @property string $comment
 * @property int    $visible
 * @property int    $user_id
 * @property string $created_at
 */
class UserTimeTracking extends ActiveRecord
{
    const TYPE_WORK_TIME = 'work_time';
    const TYPE_TIME_OFF  = 'time_off';
    const TYPE_VACATION  = 'vacation';

    /**
     * {@inheritDoc}
     */
    public static function tableName() : string
    {
        return 'users_time_tracking';
    }

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['user_id'], 'default', 'value' => Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
            [['entity_id', 'user_id', 'visible'], 'integer'],
            [['type', 'start', 'end', 'comment'], 'string'],
            [['type'], 'in', 'range' => array_keys(self::getTypeLabels())],
            [['start', 'end', 'created_at'], 'safe'],
            [['entity_id', 'type', 'start', 'end', 'user_id', 'visible', 'created_at'], 'required'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id'         => 'ID',
            'entity_id'  => 'ID сотрудника',
            'start'      => 'Начало периода',
            'end'        => 'Конец периода',
            'type'       => 'Тип',
            'comment'    => 'Комментарий',
            'user_id'    => 'Кем добавлено',
            'created_at' => 'Когда добавлено',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function find()
    {
        return new UserTimeTrackingQuery(get_called_class(), []);
    }

    /**
     * @return string[]
     */
    public static function getTypeLabels() : array
    {
        return [
            self::TYPE_WORK_TIME => 'Рабочее время',
            self::TYPE_TIME_OFF  => 'Отгул',
            self::TYPE_VACATION  => 'Отпуск',
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function getTypeLabel(string $value) : string
    {
        return self::getTypeLabels()[$value] ?? '';
    }
}