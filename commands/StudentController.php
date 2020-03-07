<?php

namespace app\commands;

use app\models\Student;
use app\models\StudentCommission;
use yii\console\Controller;
use yii\helpers\Json;
use Yii;

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

    public function actionUpdatePhones()
    {
        $file = file_get_contents(Yii::getAlias('@app/data/csvjson.json'));

        for ($i = 0; $i <= 31; ++$i) {
            $file = str_replace(chr($i), "", $file);
        }
        $file = str_replace(chr(127), "", $file);

        if (0 === strpos(bin2hex($file), 'efbbbf')) {
            $file = substr($file, 3);
        }

        $students = Json::decode($file);
        $t = Yii::$app->db->beginTransaction();
        try {
            foreach ($students as $student) {
                $studentModel = Student::find()->andWhere(['id' => $student['id']])->one();
                $studentModel->phone = (string)$student['phone'];
                if (!$studentModel->save(true, ['phone'])) {
                    throw new \Exception('Error on phones update!');
                }
            }
            $t->commit();
            echo "Success! ";
        } catch (\Exception $e) {
            $t->rollBack();
            echo "Fail! " . $e->getMessage();
        }
    }
}
