<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * 
 */
 
class Report extends Model
{
    /**
     *  метод возвращает список отчетов для создания выпадающего меню
     */
    public static function getReportTypeList()
    {
        $reportlist = [
            Yii::t('app','Common') => ['report/common'],
            Yii::t('app','Margin') => ['report/margin'],
            Yii::t('app','Payments') => ['report/index','type' => 4],
            Yii::t('app','Invoices') => ['report/index','type' => 5],
            Yii::t('app','Sales') => ['report/sale'],
            Yii::t('app','Debts') => ['report/debt'],
            Yii::t('app','Journals') => ['report/index','type' => 8],
            Yii::t('app','Accruals') => ['report/accrual'],
            Yii::t('app','Office plan') => ['report/plan'],
        ];
        return $reportlist;
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
}
