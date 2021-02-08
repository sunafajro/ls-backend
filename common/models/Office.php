<?php

namespace common\models;

use common\models\queries\OfficeQuery;
use Yii;

/**
 * Class Role
 * @package common\models
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 * @property integer $city_id
 * @property integer $num
 */
class Office extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%offices}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['num'], 'default', 'value' => 0],
            [['name'], 'string'],
            [['visible', 'city_id', 'num'], 'integer'],
            [['name', 'visible', 'city_id', 'num'], 'required'],
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
            'city_id' => Yii::t('app', 'City ID'),
            'num' => Yii::t('app', 'Num'),
        ];
    }

    /**
     * @return OfficeQuery
     */
    public static function find(): OfficeQuery
    {
        return new OfficeQuery(get_called_class(), []);
    }
}
