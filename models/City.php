<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "calc_city".
 *
 * @property integer $id
 * @property string  $name
 * @property integer $visible
 */
class City extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['visible'], 'integer'],
            [['name'],    'string', 'max' => 256],
            [['name', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => Yii::t('app', 'ID'),
            'name'    => Yii::t('app', 'Name'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    /* Метод возвращает список действующих городов */
    public static function getCitiesInUserListSimple()
    {
        $cities = [];

        /* получаем список действующих городов */
        $tmp_cities = (new \yii\db\Query())
        ->select('cc.id as id, cc.name as name')
        ->from('calc_city cc')
        ->where('cc.visible=:vis', [':vis'=>1])
        ->orderBy(['cc.name'=>SORT_ASC])
        ->all();
        /* получаем список действующих городов */
        
        /* если массив не пустой, формируем из него простой одномерный */
        if(!empty($tmp_cities)) {
            foreach($tmp_cities as $c) {
                $cities[$c['id']] = $c['name'];
            }
            unset($c);
        }
        /* если массив не пустой, формируем из него простой одномерный */
        
        return $cities;
    }

    public static function getCitiesList()
    {
        $cities = (new \yii\db\Query())
        ->select([
            'id' => 'id',
            'name' => 'name',
        ])
        ->from(self::tableName())
        ->where(['visible' => 1])
        ->orderBy(['name' => SORT_ASC])
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
