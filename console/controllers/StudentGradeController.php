<?php

namespace console\controllers;

use school\models\StudentGrade;
use yii\console\Controller;

/**
 * Class StudentGradeController
 * @package app\commands
 */
class StudentGradeController extends Controller
{
    public function actionWriteFiles()
    {
        $attestations = StudentGrade::find()->andWhere([
            'visible' => 1,
        ])->all();
        /** @var StudentGrade $attestate */
        foreach ($attestations as $attestate) {
            $attestate->writePdfFile();
        }
    }
}