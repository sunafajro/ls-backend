<?php

namespace school\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Office
 * @package school\models
 */
class Office extends \common\models\Office
{
    /**
     * возвращает список офисов по которым есть занятия в расписании
     * @return array
     */
    public static function getOfficeBySchedule(): array
    {
        return (new \yii\db\Query())
            ->select(['id' => 'o.id', 'name' => 'o.name'])
            ->distinct()
            ->from(['o' => static::tableName()])
            ->innerJoin('calc_schedule sch', 'sch.calc_office = o.id')
            ->where(['o.visible' => 1])
            ->all();
    }

    /**
     * список офисов кроме помеченных удаленными, по которым есть занятия в расписании. одномерный массив.
     * @return array
     */
    public static function getOfficeInScheduleListSimple(): array
    {
        return ArrayHelper::map(self::getOfficeBySchedule(), 'id', 'name');
    }

    /**
     * возвращает список действующих офисов
     * @return array
     */
    public function getOffices($id = null) : array
    {
        return self::getOfficesList($id);
    }

    /**
     * возвращает список действующих офисов
     * @return array
     */
    public static function getOfficesList($id = null): array
    {
        return self::find()
            ->select(['id' => 'id', 'name' => 'name'])
            ->byActive()
            ->andFilterWhere(['id' => $id])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     *  Метод возвращает список действующих офисов в виде одномерного массива
     * @return array
     */
    public static function getOfficesListSimple($id = null): ?array
    {
        $offices = ArrayHelper::map(self::getOfficesList($id) ?? [], 'id', 'name');

        return !empty($offices) ? $offices : NULL;
    }

    /**
     * возвращает список действующих офисов с привязкой к городу
     * @return array
     */
    public static function getOfficesWithCitiesList(): array
    {
        $offices = (new \yii\db\Query())
            ->select(['id' => 'o.id', 'name' => 'o.name', 'city_id' => 'c.id', 'city' => 'c.name', 'num' => 'o.num'])
            ->from(['o' => static::tableName()])
            ->innerJoin(['c' => City::tableName()], 'c.id = o.city_id')
            ->where(['o.visible' => 1])
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
}