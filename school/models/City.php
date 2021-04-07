<?php

namespace school\models;

use school\models\queries\CityQuery;
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
    public static function tableName(): string
    {
        return 'calc_city';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
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
    public function attributeLabels(): array
    {
        return [
            'id'      => Yii::t('app', 'ID'),
            'name'    => Yii::t('app', 'Name'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    /**
     * @return CityQuery
     */
    public static function find() : CityQuery
    {
        return new CityQuery(get_called_class(), []);
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
