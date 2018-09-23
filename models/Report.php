<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Moneystud;
use app\models\Office;
/**
 * 
 */
 
class Report extends Model
{
    public static function getReportTypes()
    {
        $items = [];
        if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 8) {
            $items[] = [
                'id' => 'common',
                'label' => Yii::t('app','Common'),
                'url' => '/report/common'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $items[] = [
                'id' => 'margin',
                'label' => Yii::t('app','Margin'),
                'url' => '/report/margin'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4 || (int)Yii::$app->session->get('user.ustatus') === 8) {
            $items[] = [
                'id' => 'payments',
                'label' => Yii::t('app','Payments'),
                'url' => '/report/payments'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4) {
            $items[] = [
                'id' => 'invoices',
                'label' => Yii::t('app','Invoices'),
                'url' => '/report/index?type=5'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $items[] = [
                'id' => 'sales',
                'label' => Yii::t('app','Sales'),
                'url' => '/report/sale'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4) {
            $items[] = [
                'id' => 'debts',
                'label' => Yii::t('app','Debts'),
                'url' => '/report/debt'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4) {
            $items[] = [
                'id' => 'journals',
                'label' => Yii::t('app','Journals'),
                'url' => '/report/index?type=8'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 8) {
            $items[] = [
                'id' => 'accruals',
                'label' => Yii::t('app','Accruals'),
                'url' => '/report/accrual'
            ];
        }
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $items[] = [
                'id' => 'plan',
                'label' => Yii::t('app','Office plan'),
                'url' => '/report/plan'
            ];
        }
        return $items;
    }
    /**
     *  метод возвращает список отчетов для создания выпадающего меню
     */
    public static function getReportTypeList()
    {
        $data = static::getReportTypes();
        $items = [];
        foreach($data as $r) {
            $items[$r['label']] = $r['url'];
        }
        return $items;
    }
    
    /* метод для получения списка недель (первый - последний день) указанного кода */
    public static function getWeekList($year) 
    {
        $arr = [];
        $firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
        $nextMonday     = strtotime('monday', $firstDayOfYear);
        $nextSunday     = strtotime('sunday', $nextMonday);
        
        $num = 1;
        while (date('Y', $nextMonday) == $year) {
            $arr[$num] = date('d/m', $nextMonday) . '-' . date('d/m', $nextSunday);
            $nextMonday = strtotime('+1 week', $nextMonday);
            $nextSunday = strtotime('+1 week', $nextSunday);
            $num++;
        }

        return $arr;
    }
    /* метод для получения списка недель (первый - последний день) указанного кода */

    /* метод для получения первого и последнего дня текущей недели, а так же номера недели */
    public static function getWeekInfo($day, $month, $year) {
        $today = mktime(0, 0, 0, $month, $day, $year);
        
        if(date('N', $today) == 1) {
            /* если текущий день понедельник*/
            $monday = strtotime('monday', $today);
        } else {
            /* если текущий день не понедельник*/
            $monday = strtotime('last monday', $today);
        }
        $sunday = strtotime('sunday', $monday);

        /* заполняем результирующий массив */
        $arr['start'] = $monday;
        $arr['end'] = $sunday;
        $arr['num'] = self::getNumberOfWeek($monday, $sunday);

        return $arr;
    }
    /* метод для получения первого и последнего дня текущей недели, а так же номера недели */

    /* метод возвращает номер недели по дате понедельника и воскресенья */
    public static function getNumberOfWeek($start, $end)
    {
        $firstDayOfYear = mktime(0, 0, 0, 1, 1, date('Y', $start));
        $nextMonday = strtotime('monday', $firstDayOfYear);
        $nextSunday = strtotime('sunday', $nextMonday);
        
        $num = 1;
        while (date('Y', $nextMonday) == date('Y', $start)) {
            if($start == $nextMonday && $end == $nextSunday) {
               return $num; 
            }
            $nextMonday = strtotime('+1 week', $nextMonday);
            $nextSunday = strtotime('+1 week', $nextSunday);
            $num++;
        }
        
        return 0;
    }
    /* метод возвращает номер недели по дате понедельника и воскресенья */

    // возвращает заголовки таблицы отчета по оплатам
    public static function getPaymentsReportColumns()
    {
        return [
            [
                'id' => 'id',
                'name' => '№',
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'studentId',
                'name' => Yii::t('app', 'Student ID'),
                'show' => false,
                'width' => ''
            ],
            [
                'id' => 'student',
                'name' => Yii::t('app', 'Student'),
                'show' => true,
                'width' => '30%'
            ],
            [
                'id' => 'manager',
                'name' => Yii::t('app', 'Manager'),
                'show' => true,
                'width' => '30%'
            ],
            [
                'id' => 'receipt',
                'name' => Yii::t('app', 'Receipt'),
                "show" => true,
                'width' => '10%'
            ],
            [
                'id' => 'type',
                'name' => Yii::t('app', 'Type'),
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'sum',
                'name' => Yii::t('app', 'Sum'),
                'show' => true,
                'width' => '10%'
            ],
            [
                'id' => 'active',
                'name' => Yii::t('app', 'Active'),
                'show' => false,
                'width' => ''
            ],
            [
                'id' => 'remain',
                'name' => Yii::t('app', 'Remain'),
                'show' => false,
                'width' => ''
            ],
        ];
    }

    public static function getPaymentsReportRows($start = null, $end = null, $office = null)
    {
        $result = [];
        $offices = Office::getOfficesListSimple($office);
        $payments = Moneystud::getPayments($start, $end, $office);
        foreach($payments as $p) {
            if (!isset($result[$p['office']])) {
                $result[$p['office']] = [
                    'name' => $offices[$p['office']],
                    'counts' => [
                        'cash' => 0,
                        'card' => 0,
                        'bank' => 0,
                        'all' => 0
                    ]
                ];
            }
            if (!isset($result[$p['office']][$p['date']])) {
                $result[$p['office']][$p['date']] = [
                    'counts' => [
                        'cash' => 0,
                        'card' => 0,
                        'bank' => 0,
                        'all' => 0
                    ],
                    'rows' => []
                ];
            }
            $type = '';
            if ($p['card'] != '0.00') {
                $type = Yii::t('app','Card');
                if ($p['active']) {
                    $result[$p['office']]['counts']['card'] += $p['card'];
                    $result[$p['office']][$p['date']]['counts']['card'] += $p['card'];
                }
            }
            if ($p['cash'] != '0.00') {
                $type = Yii::t('app','Cash');
                if ($p['active']) {
                    $result[$p['office']]['counts']['cash'] += $p['cash'];
                    $result[$p['office']][$p['date']]['counts']['cash'] += $p['cash'];
                }
            }
            if ($p['bank'] != '0.00') {
                $type = Yii::t('app','Bank');
                if ($p['active']) {
                    $result[$p['office']]['counts']['bank'] += $p['bank'];
                    $result[$p['office']][$p['date']]['counts']['bank'] += $p['bank'];
                }
            }
            if ($p['active']) {
                $result[$p['office']]['counts']['all'] += $p['sum'];
                $result[$p['office']][$p['date']]['counts']['all'] += $p['sum'];
            }
            $result[$p['office']][$p['date']]['rows'][] = [
                'id' => $p['id'],
                'studentId' => $p['studentId'],
                'student' => $p['student'],
                'manager' => $p['manager'],
                'receipt' => $p['receipt'],
                'type' => $type,
                'sum' => $p['sum'],
                'active' => $p['active'],
                'remain' => $p['remain']
            ];
        }

        return $result;
    }
}
