<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_kaslibro_executor".
 *
 * @property integer $id
 * @property string $name
 * @property integer $deleted
 * @property integer $user
 * @property string $date
 */
class KaslibroExecutor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_kaslibro_executor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deleted', 'user'], 'integer'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
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
            'deleted' => Yii::t('app', 'Deleted'),
            'user' => Yii::t('app', 'User'),
            'date' => Yii::t('app', 'Date'),
        ];
    }
}
