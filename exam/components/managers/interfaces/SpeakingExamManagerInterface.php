<?php

namespace exam\components\managers\interfaces;

use exam\models\SpeakingExam;
use yii\web\NotFoundHttpException;

/**
 * Interface SpeakingExamManagerInterface
 * @package exam\components\managers\interfaces
 */
interface SpeakingExamManagerInterface
{
    /**
     * @return SpeakingExam[]
     */
    public function getExams(): array;

    /**
     * @return SpeakingExam[]
     */
    public function getActiveExams(): array;

    /**
     * @param int $id
     *
     * @return SpeakingExam|null
     * @throws NotFoundHttpException
     */
    public function getExamById(int $id): ?SpeakingExam;
}