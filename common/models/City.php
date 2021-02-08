<?php

namespace common\models;

use common\models\queries\CityQuery;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class City
 * @package common\models
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 *
 * @property-read Office[] $offices
 */
class City extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%cities}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['visible'], 'integer'],
            [['name'], 'string'],
            [['name', 'visible'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    /**
     * @return CityQuery
     */
    public static function find(): CityQuery
    {
        return new CityQuery(get_called_class(), []);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffices(): ActiveQuery
    {
        return $this->hasMany(Office::class, ['city_id' => 'id']);
    }
}
