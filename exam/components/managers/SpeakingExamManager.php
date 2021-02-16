<?php

namespace exam\components\managers;

use exam\components\managers\interfaces\SpeakingExamManagerInterface;
use exam\models\SpeakingExam;
use exam\models\SpeakingExamTask;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class SpeakingExamManager
 * @package exam\components
 */
class SpeakingExamManager extends Component implements SpeakingExamManagerInterface
{
    /**
     * @return SpeakingExam[]
     */
    public function getExams(): array
    {
        return \Yii::$app->cache->getOrSet('exams_data', function() {
            $exams = [];
            foreach (['ege', 'oge'] as $fileName) {
                $filePath = \Yii::getAlias("@exams/{$fileName}.json");
                if (file_exists($filePath)) {
                    $rawExamsData = file_get_contents($filePath);
                    $jsonExamsData = json_decode($rawExamsData, true);
                    $examModels = array_map(function (array $exam) {
                        $exam['tasks'] = array_map(function(array $task) {
                            if (is_array($task)) {
                                $task = new SpeakingExamTask($task);
                            }
                            return $task;
                        }, $exam['tasks']);
                        return new SpeakingExam($exam);
                    }, $jsonExamsData);
                    $exams = array_merge($exams, $examModels);
                }
            }
            ArrayHelper::multisort($exams, ['id'], [SORT_ASC]);
            return $exams;
        }, 3600);
    }

    /**
     * @return SpeakingExam[]
     */
    public function getActiveExams(): array
    {
        return array_filter($this->getExams(), function(SpeakingExam $exam) {
            return $exam->enabled;
        });
    }

    /**
     * @param int $id
     *
     * @return SpeakingExam|null
     * @throws NotFoundHttpException
     */
    public function getExamById(int $id): ?SpeakingExam
    {
        $filtered = array_filter($this->getExams(), function(SpeakingExam $exam) use ($id) {
            return $exam->id === $id;
        });
        if (empty($filtered)) {
            throw new NotFoundHttpException(\Yii::t('app', 'The exam not found.'));
        }

        return reset($filtered);
    }
}