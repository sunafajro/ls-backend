<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "volonteers".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $date
 * @property string  $city
 */

class Volonteer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'volonteers';
    }

    public function getVolonteers()
    {
        $data = (new \yii\db\Query())
        ->select([
            'id'         => 'id',
            'name'       => 'name',
            'birthdate'  => 'birthdate',
            'city'       => 'city',
            'occupation' => 'occupation',
            'type'       => 'type',
            'social'     => 'social',
            'phone'      => 'phone',
            'note'       => 'note',
        ])
        ->from(self::tableName())
        ->where(['visible' => 1])
        ->orderby(['name' => SORT_ASC])
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
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Birthdate'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'City'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Occupation'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Type'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Social'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Phone'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Note'),
                    'show' => true
                ],
            ],
            'data'    => $data
        ];
    }
}