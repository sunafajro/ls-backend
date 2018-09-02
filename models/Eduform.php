<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "calc_eduform".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 */
class Eduform extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_eduform';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible'], 'required'],
            [['name'], 'string'],
            [['visible'], 'integer']
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

    public static function getEduforms()
    {
    	$data = (new \yii\db\Query())
    	->select('id as value, name as text')
    	->from(static::tableName())
    	->orderBy(['name'=>SORT_ASC])
        ->all();
        
        return $data;
    }
}