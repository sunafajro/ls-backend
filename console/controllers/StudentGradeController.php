<?php

namespace app\commands;

use app\models\StudentGrade;
use yii\console\Controller;

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