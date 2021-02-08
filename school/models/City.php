<?php

namespace school\models;

use Yii;

/**
 * Class City
 * @package school\models
 *
 * @property-read Office[] $offices
 */
class City extends \common\models\City
{
    /**
     * @return array
     */
    public static function getCitiesList(): array
    {
        $cities = static::find()
            ->select([
                'id' => 'id',
                'name' => 'name',
            ])
            ->byActive()
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
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
                ]
            ],
            'data'    => $cities
        ];
    }
}