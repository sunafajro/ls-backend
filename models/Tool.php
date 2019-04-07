<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\AccessRule;

/**
 * Tool класс со служебными методами.
 */
class Tool extends Model
{
    /* список месяцев */
    public static function getMonthsSimple() {
        $months = [];
        
        $data = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_month')
        ->where('visible=:vis', [':vis' => 1])
        ->orderby(['id'=>SORT_ASC])
        ->all();
        
        foreach($data as $d){
            $months[$d['id']] = $d['name'];
        }
        
        return $months;
    }

    /* список дней недели */
    public static function getDayOfWeekSimple() {
        $days = [];
        
        $data = (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_denned')
        ->where('visible=:one', [':one'=>1])
        ->orderby(['id'=>SORT_ASC])
        ->all();
        
        foreach($data as $d){
            $days[$d['id']] = mb_convert_case($d['name'], MB_CASE_TITLE, 'UTF-8');
        }
        
        return $days;
    }
    
    /* список лет с начала эксплуатации системы */
    public static function getYearsSimple() {
        $years = [];
        $year = 2011;
        
        while($year <= date('Y')){
            $years[$year] = $year;
            $year++;
        }
        
        return $years;
    }

    public function prepareDataForSelectElement($data)
    {
        $result = [];
        if(!empty($data)) {
            foreach ($data as $d) {
                $result[] = [
                    'value' => $d['id'],
                    'text' => $d['name']
                ];
            }
        }
        return $result;
    }

    public static function methodNotAllowed() {
        return [
            'code'   => 405,
            'status' => false,
            'text'   => Yii::t('app','Method not allowed!')
        ];
    }

    public static function objectNotFound() {
        return [
            'code'   => 404,
            'status' => false,
            'text'   => Yii::t('app','Object not found!')
        ];
    }
}