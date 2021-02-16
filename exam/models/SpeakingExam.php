<?php


namespace exam\models;

use Yii;
use yii\base\Model;

/**
 * Class SpeakingExam
 * @package exam\models
 *
 * @property int $id
 * @property int $num
 * @property array $tasks
 * @property int $waitTime
 * @property string $type
 * @property bool $enabled
 */
class SpeakingExam extends Model
{
    /** @var int */
    public $id;
    /** @var int */
    public $num;
    /** @var array */
    public $tasks;
    /** @var int */
    public $waitTime;
    /** @var string */
    public $type;
    /** @var bool */
    public $enabled;

    const TYPE_OGE = 'oge';
    const TYPE_EGE = 'ege';

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'num', 'waitTime'], 'integer'],
            [['type'], 'string'],
            [['type'], 'in', 'range' => [self::TYPE_OGE, self::TYPE_EGE]],
            [['enabled'], 'boolean'],
            [['tasks'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_OGE => Yii::t('app', 'OGE'),
            self::TYPE_EGE => Yii::t('app', 'EGE'),
        ];
    }

    /**
     * @param $label
     * @return string
     */
    public static function getTypeLabel($label): string
    {
        $labels = self::getTypeLabels();
        return $labels[$label] ?? '';
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'num' => Yii::t('app', 'Number'),
            'tasks' => Yii::t('app', 'Tasks'),
            'waitTime' => Yii::t('app', 'Wait time'),
            'type' => Yii::t('app', 'Type'),
            'enabled' => Yii::t('app', 'Enabled'),
        ];
    }
}