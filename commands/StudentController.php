<?php

namespace app\commands;

use app\models\Student;
use yii\console\Controller;

class StudentController extends Controller
{
    public function actionUpdateDebts()
    {
        $studentQuery = Student::find()->andWhere(['visible' => 1, 'active' => 1]);
        $count = 0;
        echo "Start active student debts update...\n";
        foreach ($studentQuery->each(100) as $student) {
            $count++;
            $student->updateInvMonDebt();
            if ($count % 100 === 0) {
                echo "Updated " . $count . " rows\n";
            }
        }
    }
}