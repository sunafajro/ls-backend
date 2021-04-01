<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\AccrualTeacher;
use school\models\Groupteacher;
use school\models\Journalgroup;
use school\models\Report;
use school\models\Service;
use school\models\Studnorm;
use school\models\Teacher;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class MarginsReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 */
class MarginsReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;

    /**
     * MarginReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($start, $end) = DateHelper::prepareMonthlyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        $config['startDate'] = $start;
        $config['endDate'] = $end;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $teachers = (new Query())
            ->select('t.id as tid, t.name as teacher_name')
            ->distinct()
            ->from(['t' => Teacher::tableName()])
            ->innerJoin(['at' => AccrualTeacher::tableName()], 't.id=at.calc_teacher')
            ->innerJoin(['jg' => Journalgroup::tableName()], 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
            ->where([
                'at.visible' => 1,
                'jg.visible' => 1,
                't.visible' => 1,
            ])
            ->andWhere(['>=', 'jg.data', $this->startDate])
            ->andWhere(['<=', 'jg.data', $this->endDate])
            ->orderby(['t.name' => SORT_ASC, 't.id' => SORT_ASC])
            ->all();

        if (!empty($teachers)) {
            $lessons = (new Query())
                ->select('t.id as tid, COUNT(jg.id) as count')
                ->from(['t' => Teacher::tableName()])
                ->innerJoin(['at' => AccrualTeacher::tableName()], 't.id=at.calc_teacher')
                ->innerJoin(['jg' => Journalgroup::tableName()], 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
                ->where([
                    'at.visible' => 1,
                    'jg.visible' => 1,
                    't.visible' => 1,
                ])
                ->andWhere(['>=', 'jg.data', $this->startDate])
                ->andWhere(['<=', 'jg.data', $this->endDate])
                ->groupby(['t.id'])
                ->all();
            $lessons = ArrayHelper::map($lessons, 'tid', 'count');

            $accruals = (new Query())
                ->select('t.id as tid, at.id as aid, at.value as value')
                ->distinct()
                ->from(['t' => Teacher::tableName()])
                ->innerJoin(['at' => AccrualTeacher::tableName()], 't.id=at.calc_teacher')
                ->innerJoin(['jg' => Journalgroup::tableName()], 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
                ->where([
                    'at.visible' => 1,
                    'jg.visible' => 1,
                    't.visible' => 1,
                ])
                ->andWhere(['>=', 'jg.data', $this->startDate])
                ->andWhere(['<=', 'jg.data', $this->endDate])
                ->all();

            $subQuery = (new Query())
                ->select('COUNT(sjg.id)')
                ->from('calc_studjournalgroup sjg')
                ->where('sjg.calc_journalgroup=jg.id and sjg.calc_statusjournal!=:status', [':status' => Journalgroup::STUDENT_STATUS_ABSENT_WARNED]);

            $income = (new Query())
                ->select('t.id as tid, jg.id as jid, gt.calc_service as sid, at.data as date')
                ->addSelect(['count' => $subQuery])
                ->from(['t' => Teacher::tableName()])
                ->innerJoin(['at' => AccrualTeacher::tableName()], 't.id=at.calc_teacher')
                ->innerJoin(['jg' => Journalgroup::tableName()], 'jg.calc_accrual=at.id and jg.calc_teacher=t.id')
                ->innerJoin(['gt' => Groupteacher::tableName()], 'jg.calc_groupteacher=gt.id')
                ->where([
                    'at.visible' => 1,
                    'jg.visible' => 1,
                    't.visible' => 1,
                ])
                ->andWhere(['>=', 'jg.data', $this->startDate])
                ->andWhere(['<=', 'jg.data', $this->endDate])
                ->all();

            $serviceIds = ArrayHelper::getColumn($income, 'sid');
            $serviceIds = array_unique($serviceIds);
            $serviceIds = array_values($serviceIds);

            $serviceHistory = (new Query())
                ->select('calc_service as sid, date as date, value as value')
                ->from('calc_servicehistory')
                ->where(['calc_service' => $serviceIds])
                ->orderBy(['calc_service' => SORT_ASC, 'id' => SORT_ASC])
                ->all();

            $lessonCost = (new Query())
                ->select('sn.value')
                ->from(['s' => Service::tableName()])
                ->leftJoin(['sn' => Studnorm::tableName()], 'sn.id=s.calc_studnorm')
                ->where(['s.id' => $serviceIds])
                ->indexBy('s.id')
                ->column();

            if (!empty($income)) {
                foreach($income as $i => $in) {
                    $cost = null;
                    foreach($serviceHistory as $sh) {
                        if ((int)$in['sid'] === (int)$sh['sid'] && $in['date'] < date('Y-m-d', strtotime($sh['date']))) {
                            $cost = $sh['value'];
                        }
                    }

                    if (is_null($cost)) {
                        $cost = $lessonCost[$in['sid']] ?? 0;
                    }

                    $income[$i]['cost'] = $cost;
                }
            }

            foreach($teachers as $i => $t) {
                $teachers[$i]['lesson_count'] = $lessons[$t['tid']];

                $teachers[$i]['sum_accrual'] = 0;
                foreach($accruals as $a) {
                    if($a['tid'] == $t['tid']) {
                        $teachers[$i]['sum_accrual'] += $a['value'];
                    }
                }
                $teachers[$i]['sum_income'] = 0;
                foreach($income as $in) {
                    if($in['tid'] == $t['tid']) {
                        $teachers[$i]['sum_income'] += $in['count'] * $in['cost'];
                    }
                }
            }
        }

        return $teachers;
    }
}