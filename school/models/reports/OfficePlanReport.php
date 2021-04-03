<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\Groupteacher;
use school\models\Report;
use school\models\Schedule;
use school\models\Service;
use school\models\Studgroup;
use school\models\Studnorm;
use yii\db\Query;

/**
 * Class OfficePlanReport
 * @package school\models\reports
 *
 * @property string $nextMonth
 * @property integer $officeId
 */
class OfficePlanReport extends Report
{
    /** @var string */
    public $nextMonth;
    /** @var string */
    public $officeId;

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $month = date('n');
        $year = date('Y');
        $monthName = DateHelper::getMonthName($month);

        $subQuery = (new Query())
            ->select('COUNT(sg.calc_studname) as cnt')
            ->from(['sg' => Studgroup::tableName()])
            ->where('sg.calc_groupteacher=gt.id and sg.visible=:one', [':one' => 1]);

        $schedule = (new Query())
            ->select('gt.id as group, sn.value as cost, sch.calc_denned as day, COUNT(sch.id) as cnt')
            ->addSelect(['pupils' => $subQuery])
            ->from(['sch' => Schedule::tableName()])
            ->leftJoin(['gt' => Groupteacher::tableName()], 'gt.id=sch.calc_groupteacher')
            ->leftJoin(['s' => Service::tableName()], 's.id=gt.calc_service')
            ->leftJoin(['sn' => Studnorm::tableName()], 'sn.id=s.calc_studnorm')
            ->where([
                'sch.visible' => 1,
                'gt.visible' => 1,
                'sch.calc_office' => $this->officeId,
            ])
            ->groupby(['gt.id', 'sn.value', 'sch.calc_denned'])
            ->all();

        $lessonPlan = 0;
        $moneyPlan = 0;

        foreach ($schedule as $i => $s) {
            if ($this->nextMonth) {
                $dt = new \DateTime(date('Y-m-d'));
                $dt->modify('next month');
                $month = $dt->format('n');
                $year = $dt->format('Y');
                $monthName = DateHelper::getMonthName($month);
            }

            $totalCount = $s['cnt'] *  DateHelper::countDaysInMonth($s['day'], $month, $year);
            $lessonPlan += $totalCount;
            $moneyPlan += $totalCount * $s['cost'] * $s['pupils'];
            $schedule[$i]['totalCount'] = $totalCount;
            $schedule[$i]['totalCost'] = $totalCount * $s['cost'];
        }

        return [$schedule, $lessonPlan, $moneyPlan, $monthName];
    }
}