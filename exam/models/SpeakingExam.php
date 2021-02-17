<?php


namespace exam\models;

use exam\components\managers\interfaces\SpeakingExamManagerInterface;
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

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function save(): bool
    {
        if ($this->validate()) {
            /** @var SpeakingExamManagerInterface $orderManager */
            $speakingExamManager = \Yii::$container->get(SpeakingExamManagerInterface::class);
            if ($speakingExamManager->updateAndSaveExam($this)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeValidate(): bool
    {
        $this->enabled = (bool)$this->enabled;
        return parent::beforeValidate();
    }
}