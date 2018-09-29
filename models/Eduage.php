<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "calc_eduage".
 *
 * @property integer $id
 * @property string $name
 * @property integer $visible
 */
class Eduage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_eduage';
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
    public static function getEduages()
    {
    	$data = (new \yii\db\Query())
    	->select('id as id, name as name')
    	->from(static::tableName())
    	->orderBy(['name'=>SORT_ASC])
        ->all();
        
        return $data;
    }
}