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

    // возвращает список офисов по которым есть занятия в расписании
    public static function getOfficeBySchedule()
    {
        $offices = (new \yii\db\Query())
        ->select('o.id as id, o.name as name')
        ->distinct()
        ->from('calc_office o')
        ->innerJoin('calc_schedule sch', 'sch.calc_office=o.id')
        ->where('o.visible=:one', [':one' => 1])
        ->all();
        return $offices;
    }
    
    /* список офисов кроме помеченных удаленными, по которым есть занятия в расписании. одномерный массив. */
    public static function getOfficeInScheduleListSimple()
    {
        $offices = [];
        $tmp_offices = static::getOfficeBySchedule();
        
        // если массив не пустой, формируем простой одноуровневый список
        if(!empty($tmp_offices)) {
            foreach($tmp_offices as $o) {
                $offices[$o['id']] = $o['name'];
            }
        }
        // если массив не пустой, формируем простой одноуровневый список

        return $offices;
    }

    /* возвращает список действующих офисов */
    public static function getOfficesList($id = null)
    {
        $offices = (new \yii\db\Query())
        ->select('co.id as id, co.name as name')
        ->from('calc_office co')
        ->where('co.visible=:vis', [':vis'=>1])
        ->andFilterWhere(['id' => $id])
        ->orderBy(['co.name' => SORT_ASC])
        ->all();
    
        return $offices;
    }

    /* возвращает список действующих офисов с привязкой к городу */
    public static function getOfficesWithCitiesList()
    {
        $offices = (new \yii\db\Query())
        ->select(['id' => 'co.id', 'name' => 'co.name', 'city_id' => 'c.id', 'city' => 'c.name', 'num' => 'co.num'])
        ->from('calc_office co')
        ->innerJoin('calc_city c', 'c.id=co.calc_city')
        ->where('co.visible=:vis', [':vis'=>1])
        ->orderBy(['c.name' => SORT_ASC, 'co.id' => SORT_ASC])
        ->all();
    
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

    /**
     *  Метод возвращает список действующих офисов в виде одномерного массива
     */
    public static function getOfficesListSimple($id = null)
    {
        $offices = [];

        $tmp_offices = static::getOfficesList($id);
        
        /* если массив не пустой, формируем из него простой одномерный */
        if(!empty($tmp_offices)) {
            foreach($tmp_offices as $o) {
                $offices[$o['id']] = $o['name'];
            }
        }
        /* если массив не пустой, формируем из него простой одномерный */

        return !empty($offices) ? $offices : NULL;
    }
}
