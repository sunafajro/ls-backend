<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\AccrualTeacher;
use school\models\Edunormteacher;
use school\models\Report;
use school\models\Teacher;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SalariesReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 * @property integer $teacherId
 */
class SalariesReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;
    /** @var integer */
    public $teacherId;

    /**
     * SalariesReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($start, $end) = DateHelper::prepareMonthlyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        if (empty($config['startDate']) || empty($config['endDate'])) {
            $start = \DateTime::createFromFormat('Y-m-d', $start);
            $start->modify('-1 months');
            $end = clone($start);
            $end->modify('last day of this month');
            $start = $start->format('Y-m-d');
            $end = $end->format('Y-m-d');
        }
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
            ->select('t.name')
            ->from(['t' => Teacher::tableName()])
            ->innerJoin(['acc' => AccrualTeacher::tableName()], 'acc.calc_teacher = t.id')
            ->where([
                'acc.visible' => 1,
                't.visible' => 1,
                't.old' => 0
            ])
            ->andFilterWhere(['<=', 'acc.data', $this->endDate ?? null])
            ->andFilterWhere(['>=', 'acc.data', $this->startDate ?? null])
            ->andFilterWhere(['t.id' => $this->teacherId ?? null])
            ->indexBy('t.id')
            ->orderBy(['t.name' => SORT_ASC])
            ->column();

        $salaries = [];
        if (!empty($teachers)) {
            $teacherKeys = array_keys($teachers);
            $teacherTaxes = ArrayHelper::map(Edunormteacher::getTaxes($teacherKeys), 'entId', 'value');
            $accruals = AccrualTeacher::getAccrualsByTeachers(
                $this->startDate,
                $this->endDate,
                $teacherKeys
            );
            foreach($accruals as $a) {
                if (!isset($salaries[$a['teacherId']])) {
                    $salaries[$a['teacherId']] = [
                        'name' => $teachers[$a['teacherId']],
                        'counts' => [
                            'all' => 0
                        ],
                        'rows' => []
                    ];
                }
                if ($a['sum'] != '0.00') {
                    $salaries[$a['teacherId']]['counts']['all'] += $a['sum'];
                }
                $salaries[$a['teacherId']]['rows'][] = [
                    'id' => $a['id'],
                    'teacherId' => $a['teacherId'],
                    'teacher' => $teachers[$a['teacherId']],
                    'date' => $a['date'],
                    'hours' => $a['hours'],
                    'tax' => isset($teacherTaxes[$a['tax']]) ? $teacherTaxes[$a['tax']] : 0,
                    'sum' => $a['sum'],
                ];

            }
        }

        return [$salaries, $this->teacherId];
    }
}