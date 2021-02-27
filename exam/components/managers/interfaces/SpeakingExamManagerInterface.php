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
     * Считывает список экзаменов из json файла, загружает в модели, кеширует и возвращает в виде массива в качестве результата.
     * @return SpeakingExam[]
     */
    public function getExams(): array;

    /**
     * Возвращает массив экзаменов в активном состоянии (enabled === true).
     * @param string|null $type
     * @return SpeakingExam[]
     */
    public function getActiveExams(string $type = null): array;

    /**
     * Ищет и возвращает экзамен по его id.
     * @param int $id
     *
     * @return SpeakingExam|null
     * @throws NotFoundHttpException
     */
    public function getExamById(int $id): ?SpeakingExam;

    /**
     * Сохраняет обновленный экзамен обратно в json файл, актуализирует кеш экзаменов
     * @param SpeakingExam $examModel
     * @return bool
     */
    public function updateAndSaveExam(SpeakingExam $examModel): bool;
}