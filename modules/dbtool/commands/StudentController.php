<?php

namespace app\modules\dbtool\commands; 
 
use yii\helpers\Console;
use yii\console\Controller;
 
/**
 * @package app\commands
 */
class StudentController extends Controller
{
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