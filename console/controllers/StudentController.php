<?php

namespace console\controllers;

use school\models\Student;
use school\models\StudentCommission;
use yii\console\Controller;
use yii\helpers\Json;
use Yii;

/**
 * Class StudentController
 * @package app\commands
 */
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

    // updates students with office
    public function actionOffice()
    {
        $offset = 0;
        $limit = 20;
        $cnt = (new \yii\db\Query())
            ->select('count(s.id) as num')
            ->from(['s' => 'calc_studname'])
            ->leftJoin('student_office so', 's.id=so.student_id')
            ->where(['so.office_id' => NULL])
            ->one();
        if (isset($cnt) && isset($cnt['num']) && (int)$cnt['num'] > 1) {
            echo 'Student List total count: ' . (int)$cnt['num'] . PHP_EOL;
            for ($i = 0; $i < (int)$cnt['num']; $i = $i + $limit) {
                $offset = $offset + $limit;
                $students = (new \yii\db\Query())
                    ->select(['id' => 's.id'])
                    ->from(['s' => 'calc_studname'])
                    ->leftJoin('student_office so', 's.id=so.student_id')
                    ->where(['so.office_id' => NULL])
                    ->limit($limit)
                    ->offset($offset)
                    ->all();
                echo 'Student List from ' . $offset . ' to ' . ($offset + $limit) . ':' . PHP_EOL;
                if (isset($students) && count($students)) {
                    foreach($students as $s) {
                        $office = (new \yii\db\Query())
                            ->select(['id' => 'calc_office'])
                            ->from(['calc_invoicestud'])
                            ->where(['visible' => 1, 'calc_studname' => $s['id']])
                            ->orderBy(['data' => SORT_DESC])
                            ->one();
                        if (isset($office) && isset($office['id']) && (int)$office['id'] > 0) {
                            $associate = (new \yii\db\Query())
                                ->select(['id' => 'id'])
                                ->from(['student_office'])
                                ->where(['student_id' => $s['id'], 'office_id' => $office['id']])
                                ->one();
                            if (isset($associate) && isset($associate['id']) && (int)$associate['id'] > 0) {
                                echo "Student " . $s['id'] . " already has an office associated." . PHP_EOL;
                            } else {
                                $db = (new \yii\db\Query())
                                    ->createCommand()
                                    ->insert('student_office',
                                        [
                                            'student_id' => $s['id'],
                                            'office_id' => $office['id'],
                                        ])
                                    ->execute();
                                echo 'INSERT INTO student_office(student_id, office_id) VALUES(' . $s['id'] . ',' . $office['id'] . ');' . PHP_EOL;
                            }
                        } else {
                            // search for source call
                            $call = (new \yii\db\Query())
                                ->select(['id' => 'calc_office'])
                                ->from(['c' => 'calc_call'])
                                ->innerJoin('calc_studname s', 's.id=c.calc_studname')
                                ->where(['c.calc_studname' => $s['id']])
                                ->andWhere(['!=', 'c.user_transform', 0])
                                ->andWhere(['!=', 'c.data_transform', '0000-00-00'])
                                ->one();
                            if (isset($call) && isset($call['id']) && (int)$call['id'] > 0) {
                                $associate = (new \yii\db\Query())
                                    ->select(['id' => 'id'])
                                    ->from(['student_office'])
                                    ->where(['student_id' => $s['id'], 'office_id' => $call['id']])
                                    ->one();
                                if (isset($associate) && isset($associate['id']) && (int)$associate['id'] > 0) {
                                    echo "Student " . $s['id'] . " already has an office associated." . PHP_EOL;
                                } else {
                                    $db = (new \yii\db\Query())
                                        ->createCommand()
                                        ->insert('student_office',
                                            [
                                                'student_id' => $s['id'],
                                                'office_id' => $call['id'],
                                            ])
                                        ->execute();
                                    echo 'INSERT INTO student_office(student_id, office_id) VALUES(' . $s['id'] . ',' . $call['id'] . ');' . PHP_EOL;
                                }
                            } else {
                                echo "Not found call for student " . $s['id'] . PHP_EOL;
                            }
                        }
                    }
                }
            }
        }
    }
    public function actionInactive()
    {
        $offset = 0;
        $limit = 20;
        $cnt = (new \yii\db\Query())
            ->select('count(s.id) as num')
            ->from(['s' => 'calc_studname'])
            ->where(['s.active' => 1])
            ->one();
        if (isset($cnt) && isset($cnt['num']) && (int)$cnt['num'] > 1) {
            echo 'Student List total count: ' . (int)$cnt['num'] . PHP_EOL;
            for ($i = 0; $i < (int)$cnt['num']; $i = $i + $limit) {
                $offset = $offset + $limit;
                $students = (new \yii\db\Query())
                    ->select(['id' => 's.id'])
                    ->from(['s' => 'calc_studname'])
                    ->where(['s.active' => 1])
                    ->limit($limit)
                    ->offset($offset)
                    ->all();
                echo 'Student List from ' . $offset . ' to ' . ($offset + $limit) . ':' . PHP_EOL;
                if (isset($students) && count($students)) {
                    foreach($students as $s) {
                        $invoice = (new \yii\db\Query())
                            ->select(['date' => 'data'])
                            ->from(['calc_invoicestud'])
                            ->where(['visible' => 1, 'calc_studname' => $s['id']])
                            ->orderBy(['data' => SORT_DESC])
                            ->one();
                        if (isset($invoice) && isset($invoice['date'])) {
                            if ($invoice['date'] < '2018-01-01') {
                                // $db = (new \yii\db\Query())
                                // ->createCommand()
                                // ->update('calc_studname',
                                // [
                                //     'active' => 0
                                // ],
                                // ['id' => $s['id']])
                                // ->execute();
                                echo 'UPDATE calc_studname SET active = 0 where id = ' . $s['id'] . ';' . PHP_EOL;
                            } else {
                                echo "Student " . $s['id'] . " last invoice date " . $invoice['date'] . PHP_EOL;
                            }
                        } else {
                            echo 'UPDATE calc_studname SET active = 0 where id = ' . $s['id'] . ';' . PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}
