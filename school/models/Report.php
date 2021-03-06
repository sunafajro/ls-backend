<?php

namespace school\models;

use common\components\helpers\DateHelper;
use school\models\Edunormteacher;
use school\models\Groupteacher;
use school\models\Journalgroup;
use school\models\Moneystud;
use school\models\Service;
use school\models\Teacher;
use school\models\Timenorm;
use DateTime;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class Report
 * @package school\models
 */
class Report extends Model
{
    /**
     * @return array
     */
    public static function getReportTypes() : array
    {
        $roleId = (int)Yii::$app->session->get('user.ustatus');
        $items = [];
        if (in_array($roleId, [3, 8])) {
            $items[] = [
                'id'    => 'common',
                'label' => Yii::t('app','Common'),
                'url'   => Url::to(['report/common']),
            ];
        }
        if (in_array($roleId, [3])) {
            $items[] = [
                'id' => 'margin',
                'label' => Yii::t('app','Margin'),
                'url' => Url::to(['report/margin']),
            ];
        }
        if (in_array($roleId, [3, 4, 8])) {
            $items[] = [
                'id' => 'payments',
                'label' => Yii::t('app','Payments'),
                'url' => Url::to(['report/payments']),
            ];
        }
        if (in_array($roleId, [3, 4])) {
            $items[] = [
                'id' => 'invoices',
                'label' => Yii::t('app','Invoices'),
                'url' => Url::to(['report/invoices']),
            ];
        }
        if (in_array($roleId, [3])) {
            $items[] = [
                'id' => 'sales',
                'label' => Yii::t('app','Sales'),
                'url' => Url::to(['report/sale']),
            ];
        }
        if (in_array($roleId, [3, 4])) {
            $items[] = [
                'id' => 'debts',
                'label' => Yii::t('app','Debts'),
                'url' => Url::to(['report/debt']),
            ];
        }
        if (in_array($roleId, [3, 4])) {
            $items[] = [
                'id' => 'journals',
                'label' => Yii::t('app','Journals'),
                'url' => Url::to(['report/journals']),
            ];
        }
        if (in_array($roleId, [3, 8])) {
            $items[] = [
                'id' => 'accruals',
                'label' => Yii::t('app','Accruals'),
                'url' => Url::to(['report/accrual']),
            ];
        }
        if (in_array($roleId, [3, 8])) {
            $items[] = [
                'id' => 'salaries',
                'label' => Yii::t('app','Salaries'),
                'url' => Url::to(['report/salaries']),
            ];
        }
        if (in_array($roleId, [3])) {
            $items[] = [
                'id' => 'plan',
                'label' => Yii::t('app','Office plan'),
                'url' => Url::to(['report/plan']),
            ];
        }
        if (in_array($roleId, [3, 4, 6])) {
            $items[] = [
                'id' => 'lessons',
                'label' => Yii::t('app','Lessons'),
                'url' => Url::to(['report/lessons']),
            ];
        }
        if (in_array($roleId, [3, 4, 6])) {
            $items[] = [
                'id' => 'teacher-hours',
                'label' => Yii::t('app','Teacher hours'),
                'url' => Url::to(['report/teacher-hours']),
            ];
        }
        if (in_array($roleId, [3, 4, 8])) {
            $items[] = [
                'id' => 'commissions',
                'label' => Yii::t('app','Commissions'),
                'url' => Url::to(['report/commissions']),
            ];
        }

        return $items;
    }

    /**
     * Список отчетов для создания выпадающего меню
     * 
     * @return array
     */
    public static function getReportTypeList() : array
    {
        return ArrayHelper::map(static::getReportTypes(), 'label', 'url');
    }

    /**
     * @param null $start
     * @param null $end
     * @param null $office
     *
     * @return array
     */
    public static function getPayments($start = null, $end = null, $office = null) : array
    {
        $result = [];
        $payments = Moneystud::getPayments($start, $end, $office);
        foreach($payments as $p) {
            if (!isset($result[$p['officeId']])) {
                $result[$p['officeId']] = [
                    'name' => $p['office'],
                    'counts' => [
                        Moneystud::PAYMENT_TYPE_CASH => 0,
                        Moneystud::PAYMENT_TYPE_CARD => 0,
                        Moneystud::PAYMENT_TYPE_BANK => 0,
                        'all' => 0
                    ]
                ];
            }
            if (!isset($result[$p['officeId']][$p['date']])) {
                $result[$p['officeId']][$p['date']] = [
                    'counts' => [
                        Moneystud::PAYMENT_TYPE_CASH => 0,
                        Moneystud::PAYMENT_TYPE_CARD => 0,
                        Moneystud::PAYMENT_TYPE_BANK => 0,
                        'all' => 0
                    ],
                    'rows' => []
                ];
            }
            $type = '';
            if ($p['card'] != '0.00') {
                $type = Moneystud::PAYMENT_TYPE_CARD;
                if ($p['active'] && !$p['remain']) {
                    $result[$p['officeId']]['counts'][$type] += $p[$type];
                    $result[$p['officeId']][$p['date']]['counts'][$type] += $p[$type];
                }
            }
            if ($p['cash'] != '0.00') {
                $type = Moneystud::PAYMENT_TYPE_CASH;
                if ($p['active'] && !$p['remain']) {
                    $result[$p['officeId']]['counts'][$type] += $p[$type];
                    $result[$p['officeId']][$p['date']]['counts'][$type] += $p[$type];
                }
            }
            if ($p['bank'] != '0.00') {
                $type = Moneystud::PAYMENT_TYPE_BANK;
                if ($p['active'] && !$p['remain']) {
                    $result[$p['officeId']]['counts'][$type] += $p[$type];
                    $result[$p['officeId']][$p['date']]['counts'][$type] += $p[$type];
                }
            }
            if ($p['active'] && !$p['remain']) {
                $result[$p['officeId']]['counts']['all'] += $p['sum'];
                $result[$p['officeId']][$p['date']]['counts']['all'] += $p['sum'];
            }
            $result[$p['officeId']][$p['date']]['rows'][] = [
                'id'                 => $p['id'],
                'studentId'          => $p['studentId'],
                'student'            => $p['student'],
                'manager'            => $p['manager'],
                'receipt'            => $p['receipt'],
                'type'               => $type,
                'sum'                => $p['sum'],
                'active'             => $p['active'],
                'remain'             => $p['remain'],
                'notificationId'     => $p['notificationId'],
                'notification'       => $p['notification'],
            ];
        }

        return $result;
    }

    /**
     * возвращает заголовки таблицы отчета по оплатам
     *
     * @return array[]
     */
    public static function getSalariesReportColumns() : array
    {
        return [
            [
                'id' => 'id',
                'name' => '№',
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'teacherId',
                'name' => Yii::t('app', 'Teacher ID'),
                'show' => false,
                'width' => ''
            ],
            [
                'id' => 'teacher',
                'name' => Yii::t('app', 'Teacher'),
                'show' => false,
                'width' => '30%'
            ],
            [
                'id' => 'date',
                'name' => Yii::t('app', 'Date'),
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'hours',
                'name' => Yii::t('app', 'Hours'),
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'tax',
                'name' => Yii::t('app', 'Tax'),
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'sum',
                'name' => Yii::t('app', 'Sum'),
                'show' => true,
                'width' => '10%'
            ],
        ];
    }

    /**
     * @param null $params
     *
     * @return array
     */
    public static function getSalariesReportRows($params = null) : array
    {
        $result = [];
        $teachersKeyVal = [];
        $teachersKey = [];
        $teacherTaxes = [];
        $teachers = Teacher::getTeachersByAccruals($params);
        if (count($teachers['rows'])) {
            foreach($teachers['rows'] as $t) {
                $teachersKeyVal[$t['id']] = $t['name'];
                $teachersKey[] = $t['id'];
            }
            $teachersKey = array_unique($teachersKey);
            $taxes = Edunormteacher::getTaxes($teachersKey);
            foreach($taxes as $t) {
                $teacherTaxes[$t['entId']] = $t['value'];
            }
            $accruals = AccrualTeacher::getAccrualsByTeachers(
                $params['start'],
                $params['end'],
                $teachersKey
            );
            foreach($accruals as $a) {
                if (!isset($result[$a['teacherId']])) {
                    $result[$a['teacherId']] = [
                        'name' => $teachersKeyVal[$a['teacherId']],
                        'counts' => [
                            'all' => 0
                        ],
                        'rows' => []
                    ];
                }
                if ($a['sum'] != '0.00') {
                    $result[$a['teacherId']]['counts']['all'] += $a['sum'];
                }
                $result[$a['teacherId']]['rows'][] = [
                    'id' => $a['id'],
                    'teacherId' => $a['teacherId'],
                    'teacher' => $teachersKeyVal[$a['teacherId']],
                    'date' => $a['date'],
                    'hours' => $a['hours'],
                    'tax' => isset($teacherTaxes[$a['tax']]) ? $teacherTaxes[$a['tax']] : 0,
                    'sum' => $a['sum'],
                ];

            }
        }
        return [
            'rows' => $result,
            'total' => $teachers['total']
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getTeacherHours(array $params) : array
    {
        $startOfWeek = DateTime::createFromFormat('Y-m-d', $params['start']);
        $endOfWeek = DateTime::createFromFormat('Y-m-d', $params['end']);
        if (!$startOfWeek) {
            $startOfWeek = DateHelper::getStartOfWeek(null, false);
        }
        if (!$endOfWeek) {
            $endOfWeek = DateHelper::getEndOfWeek(null, false);
        }
        $checkEndOfWeek = clone($startOfWeek);
        $checkEndOfWeek = $checkEndOfWeek->modify('+6 day');
        if ($endOfWeek->format('Y-m-d') > $checkEndOfWeek->format('Y-m-d')) {
            $endOfWeek = clone($checkEndOfWeek);
        }

        $teachers = (new Query())
        ->select(['id' => 't.id', 'name' => 't.name'])
        ->from(['t' => Teacher::tableName()])
        ->innerJoin(['j' => 'calc_journalgroup'], 'j.calc_teacher = t.id')
        ->where([
            't.visible' => 1,
            't.old'     => 0,
            'j.visible' => 1,
        ])
        ->andFilterWhere(['>=', 'j.data', $startOfWeek->format('Y-m-d')])
        ->andFilterWhere(['<=', 'j.data', $endOfWeek->format('Y-m-d')])
        ->andFilterWhere(['t.id' => $params['tid'] ?? null])
        ->groupBy(['t.id', 't.name'])
        ->orderBy(['t.name' => SORT_ASC])
        ->all();
        $ids = ArrayHelper::getColumn($teachers, 'id');

        $lessons = (new Query())
        ->select([
            'teacherId' => 't.id',
            'teacher'   => 't.name',
            'date'      => 'j.data',
            'period'    => new Expression('CONCAT(j.time_begin, " - ", j.time_end)'),
            'hours'     => 'tn.value'
        ])
        ->from(['j' => Journalgroup::tableName()])
        ->innerJoin(['t' => Teacher::tableName()], 'j.calc_teacher = t.id')
        ->innerJoin(['g' => Groupteacher::tableName()], 'j.calc_groupteacher = g.id')
        ->innerJoin(['s' => Service::tableName()], 'g.calc_service = s.id')
        ->innerJoin(['tn' => Timenorm::tableName()], 's.calc_timenorm = tn.id')
        ->andWhere([
            'j.visible' => 1,
        ])
        ->andFilterWhere(['j.calc_teacher' => $params['tid'] ?? $ids])
        ->andFilterWhere(['>=', 'j.data', $startOfWeek->format('Y-m-d')])
        ->andFilterWhere(['<=', 'j.data', $endOfWeek->format('Y-m-d')])
        ->orderBy(['j.data' => SORT_ASC, 'j.time_begin' => SORT_ASC])
        ->all();

        $result = [];
        while ($startOfWeek->format('Y-m-d') <= $endOfWeek->format('Y-m-d')) {
            if (!isset($result[$startOfWeek->format('Y-m-d')])) {
                $result[$startOfWeek->format('Y-m-d')] = [];
            }
            foreach ($lessons ?? [] as $lesson) {
                if ($lesson['date'] === $startOfWeek->format('Y-m-d')) {
                    if (!isset($result[$lesson['date']][$lesson['teacherId']])) {
                        $result[$lesson['date']][$lesson['teacherId']] = [];
                    }
                    $lesson['periodHours'] = isset($lesson['period']) && $lesson['period'] ? DateHelper::strIntervalToCount($lesson['period'], ' - ', 'H:i', 'h') : null;
                    $result[$lesson['date']][$lesson['teacherId']][] = $lesson;
                }
            }
            $startOfWeek->modify('+1 day');
        }
        
        return [
            'teachers' => ArrayHelper::map($teachers, 'id', 'name'),
            'hours' => $result
        ];
    }
}
