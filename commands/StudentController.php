<?php

namespace app\commands;

use app\models\Student;
use app\models\StudentCommission;
use yii\console\Controller;

class StudentController extends Controller
{
    public function actionUpdateDebts()
    {
        $studentQuery = Student::find()->andWhere(['visible' => 1, 'active' => 1]);
        $count = 0;
        echo "Start updating student debts...\n";
        foreach ($studentQuery->each(100) as $student) {
            $count++;
            $student->updateInvMonDebt();
            if ($count % 100 === 0) {
                echo "Updated " . $count . " rows\n";
            }
        }
    }

    public function actionUpdateCommissionsOffice()
    {
        $commissionQuery = StudentCommission::find()->andWhere(['visible' => 1, 'office_id' => null]);
        $count = 0;
        echo "Start updating student commissions...\n";
        foreach ($commissionQuery->each(100) as $commission) {
            $count++;
            $office = $commission->user->office ?? null;
            if ($office) {

                $commission->office_id = $office->id;
                $commission->save(true, ['office_id']);
            }
            if ($count % 100 === 0) {
                echo "Updated " . $count . " rows\n";
            }
        }
    }
}