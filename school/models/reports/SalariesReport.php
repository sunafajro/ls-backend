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
 * @property integer $month
 * @property integer $year
 * @property integer $teacherId
 *
 * @property string $_startDate
 * @property string $_endDate
 */
class SalariesReport extends Report
{
    public $month;
    public $year;
    public $teacherId;

    private $_startDate;
    private $_endDate;
    /**
     * SalariesReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($startDate, $endDate) = DateHelper::getDateRangeByMonth($config['month'], $config['year'], false);
        if (empty($config['month']) || empty($config['year'])) {
            $startDate->modify('-1 months');
            $endDate = clone($startDate);
            $endDate->modify('last day of this month');
        }
        $config['month'] = (int)$startDate->format('m');
        $config['year'] = (int)$startDate->format('Y');
        $this->_startDate = $startDate->format('Y-m-d');
        $this->_endDate = $endDate->format('Y-m-d');

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
            ->andFilterWhere(['<=', 'acc.data', $this->_endDate ?? null])
            ->andFilterWhere(['>=', 'acc.data', $this->_startDate ?? null])
            ->andFilterWhere(['t.id' => $this->teacherId ?? null])
            ->indexBy('t.id')
            ->orderBy(['t.name' => SORT_ASC])
            ->column();

        $salaries = [];
        if (!empty($teachers)) {
            $teacherKeys = array_keys($teachers);
            $teacherTaxes = ArrayHelper::map(Edunormteacher::getTaxes($teacherKeys), 'entId', 'value');
            $accruals = AccrualTeacher::getAccrualsByTeachers(
                $this->_startDate,
                $this->_endDate,
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

        return [$salaries, $this->month, $this->year, $this->teacherId];
    }
}