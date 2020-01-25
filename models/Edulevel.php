<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "calc_edulevel".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 */
class Edulevel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_edulevel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible'], 'required'],
            [['name'], 'string'],
            [['visible'], 'integer'],
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
        ];
    }

    public static function getEduLevels()
    {
    	$data = (new \yii\db\Query())
    	->select('id as id, name as name')
    	->from(static::tableName())
        ->where(['visible' => 1])
    	->orderBy(['name' => SORT_ASC])
        ->all();
        
        return $data;
    }

    public static function getEduLevelsSimple()
    {
        return ArrayHelper::map(self::getEduLevels(), 'id', 'name');
    }
}
