<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_office".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 * @property integer $calc_city
 * @property integer $num
 */
class Office extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_office';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible', 'calc_city', 'num'], 'required'],
            [['name'], 'string'],
            [['visible', 'calc_city', 'num'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'visible' => Yii::t('app', 'Visible'),
            'calc_city' => Yii::t('app', 'Calc City'),
            'num' => Yii::t('app', 'Num'),
        ];
    }

    /* список офисов кроме помеченных удаленными. многомерный массив. */
    public static function getOfficesList()
    {
        /* получаем список доступных офисов */
        $offices = (new \yii\db\Query())
        ->select('co.id as id, co.name as name')
        ->from('calc_office co')
        ->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['co.name' => SORT_ASC])
        ->all();
        /* получаем список доступных офисов */
    
        return $offices;
    }

    /* список офисов кроме помеченных удаленными, по которым есть занятия в расписании. одномерный массив. */
    public static function getOfficeInScheduleListSimple()
    {
        $offices = [];

        // получаем список офисов по которым есть занятия в расписании
        $tmp_offices = (new \yii\db\Query())
        ->select('o.id as id, o.name as name')
        ->distinct()
        ->from('calc_office o')
        ->innerJoin('calc_schedule sch', 'sch.calc_office=o.id')
        ->where('o.visible=:one', [':one' => 1])
        ->all();
        // получаем список офисов
        
        // если массив не пустой, формируем простой одноуровневый список
        if(!empty($tmp_offices)) {
            foreach($tmp_offices as $o) {
                $offices[$o['id']] = $o['name'];
            }
        }
        // если массив не пустой, формируем простой одноуровневый список

        return $offices;
    }


    /* список офисов кроме помеченных удаленными, с привязкой к городам. многомерный массив. */
    public static function getOfficesWithCitiesList()
    {
        /* получаем список доступных офисов с привязкой к городу */ 
        $offices = (new \yii\db\Query())
        ->select(['id' => 'co.id', 'name' => 'co.name', 'city_id' => 'c.id', 'city' => 'c.name', 'num' => 'co.num'])
        ->from('calc_office co')
        ->innerJoin('calc_city c', 'c.id=co.calc_city')
        ->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['c.name' => SORT_ASC, 'co.id' => SORT_ASC])
        ->all();
        /* получаем список доступных офисов */
    
        return [
            'columns' => [
                [
                    'id'   => 'id',
                    'name' => '№',
                    'show' => true
                ],
                [
                    'id'   => 'name',
                    'name' => Yii::t('app', 'Name'),
                    'show' => true
                ],
                [
                    'id'   => 'city_id',
                    'name' => Yii::t('app', 'City ID'),
                    'show' => false
                ],
                [
                    'id'   => 'city',
                    'name' => Yii::t('app', 'City'),
                    'show' => true
                ],
                [
                    'id'   => 'num',
                    'name' => Yii::t('app', 'Count'),
                    'show' => false
                ]
            ],
            'data'    => $offices
        ];
    }

    /* список офисов кроме помеченных удаленными. одномерный массив. */
    public static function getOfficesListSimple()
    {
        $tmp_offices = static::getOfficesList();
        $offices = [];
        /* если массив не пустой, формируем из него простой одномерный */
        if(!empty($tmp_offices)) {
            foreach($tmp_offices as $o) {
                $offices[$o['id']] = $o['name'];
            }
        }
        /* если массив не пустой, формируем из него простой одномерный */

        return !empty($offices) ? $offices : NULL;
    }

    /* список офисов кроме помеченных удаленными. многомерный массив. */
    public static function getOfficeForBootstrapSelect()
    {
        $tmp_offices = static::getOfficesList();
        $offices = [];
        foreach($tmp_offices as $o) {
            $offices[] = [
              'value' => $o['id'],
              'text' => $o['name']
            ];
        }
        return $offices;
    }

    public static function getOfficeByScheduleForBootstrapSelect()
    {
        $tmp_offices = static::getOfficeInScheduleListSimple();
        $offices = [];
        foreach($tmp_offices as $key => $value) {
            $offices[] = [
              'value' => $key,
              'text' => $value
            ];
        }
        return $offices;
    }
}
