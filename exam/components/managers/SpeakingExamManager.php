<?php

namespace exam\components\managers;

use exam\components\managers\interfaces\SpeakingExamManagerInterface;
use exam\models\SpeakingExam;
use exam\models\SpeakingExamTask;
use yii\base\Component;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * Class SpeakingExamManager
 * @package exam\components
 */
class SpeakingExamManager extends Component implements SpeakingExamManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getExams(): array
    {
        return \Yii::$app->cache->getOrSet('speaking_exams_data', function() {
            $exams = [];
            foreach (['ege', 'oge'] as $fileName) {
                $filePath = \Yii::getAlias("@examData/{$fileName}.json");
                if (file_exists($filePath)) {
                    $rawExamsData = file_get_contents($filePath);
                    $jsonExamsData = Json::decode($rawExamsData, true);
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
     * {@inheritDoc}
     */
    public function getActiveExams(): array
    {
        return array_filter($this->getExams(), function(SpeakingExam $exam) {
            return $exam->enabled;
        });
    }

    /**
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     */
    public function updateAndSaveExam(SpeakingExam $examModel): bool
    {
        $filtered = array_filter($this->getExams(), function(SpeakingExam $exam) use ($examModel) {
            return $exam->id !== $examModel->id;
        });
        $filtered[] = $examModel;
        $arrayExamsData = array_map(function(SpeakingExam $exam) {
            $exam->tasks = array_map(function(SpeakingExamTask $task) {
                return $task->toArray();
            }, $exam->tasks);
            return $exam->toArray();
        }, $filtered);

        foreach (['ege', 'oge'] as $fileName) {
            $filePath = \Yii::getAlias("@examData/{$fileName}.json");
            $examsData = array_filter($arrayExamsData, function(array $exam) use ($fileName) {
                return $exam['type'] === $fileName;
            });
            file_put_contents($filePath, Json::encode($examsData));
        }
        \Yii::$app->cache->delete('speaking_exams_data');
        return true;
    }
}