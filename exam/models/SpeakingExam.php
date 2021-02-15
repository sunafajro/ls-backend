<?php


namespace exam\models;

use Yii;

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
class SpeakingExam extends \yii\base\Model
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
            'tasks' => Yii::t('app', 'Exam tasks'),
            'waitTime' => Yii::t('app', 'Wait time'),
            'type' => Yii::t('app', 'Type'),
            'enabled' => Yii::t('app', 'Enabled'),
        ];
    }

    /**
     * @return SpeakingExam[]
     */
    public static function getExams(): array
    {
        return Yii::$app->cache->getOrSet('exams_data', function() {
            $exams = [];
            foreach (['ege', 'oge'] as $fileName) {
                $filePath = Yii::getAlias("@exams/{$fileName}.json");
                if (file_exists($filePath)) {
                    $rawExamsData = file_get_contents($filePath);
                    $jsonExamsData = json_decode($rawExamsData, true);
                    $examModels = array_map(function (array $exam) {
                        return new self($exam);
                    }, $jsonExamsData);
                    $exams = array_merge($exams, $examModels);
                }
            }
            return $exams;
        }, 3600);
    }

    /**
     * @return SpeakingExam[]
     */
    public static function getActiveExams(): array
    {
        return array_filter(self::getExams(), function(SpeakingExam $exam) {
            return $exam->enabled;
        });
    }
}