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
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        $roleId = $auth->roleId;
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
     * @return array
     */
    public function prepareReportData(): array
    {
        return [];
    }
}
