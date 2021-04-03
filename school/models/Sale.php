<?php

namespace school\models;

use Yii;

/**
 * This is the model class for table "calc_sale".
 *
 * @property integer $id
 * @property string  $name
 * @property integer $visible
 * @property integer $procent
 * @property integer $base
 * @property double  $value
 * @property string  $data
 */
class Sale extends \yii\db\ActiveRecord
{
    const TYPE_RUB = 0;
    const TYPE_PERCENT = 1;
    const TYPE_PERMAMENT = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'calc_sale';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'procent', 'value'], 'required'],
            [['visible', 'procent', 'base'], 'integer'],
            [['name'],    'string'],
            [['value'],   'number'],
            [['data'],    'safe'],
            [['base'],    'default', 'value' => 0],
            [['visible'], 'default', 'value' => 1],
            [['data'],    'default', 'value' => date('Y-m-d')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => '№',
            'name'    => Yii::t('app','Sale name'),
            'visible' => Yii::t('app','Active'),
            'procent' => Yii::t('app','Type'),
            'value'   => Yii::t('app','Value'),
			'base'    => Yii::t('app','Client account size'),
            'data'    => Yii::t('app','Created at'),
        ];
    }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_RUB       => Yii::t('app', 'Ruble sale'),
            self::TYPE_PERCENT   => Yii::t('app', 'Percent sale'),
            self::TYPE_PERMAMENT => Yii::t('app', 'Permament sale'),  
        ];
    }    

    public static function getTypeLabel($typeId)
    {
        $labels = self::getTypeLabels();
        return $labels[$typeId] ?? '';
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getSalesTableHeader()
    {
        $header = [
          [ 'id' => 'id', 'title' => '#' ],
          [ 'id' => 'name', 'title' => Yii::t('app', 'Nomination') ],
          [ 'id'=> 'value', 'title'=> Yii::t('app', 'Value') ],
          [ 'id'=> 'date', 'title'=> Yii::t('app', 'Date') ],
          [ 'id'=> 'actions', 'title'=> Yii::t('app', 'Act.') ]
        ];

        return $header;
    }

    public static function getSalesList($filters)
    {
        $sales = (new \yii\db\Query())
        ->select('s.id as id, s.name as name, s.value as value, s.data as date, s.visible as visible, s.base as accountsize, s.procent as type')
        ->from('calc_sale s')
        ->where('s.visible=:vis', [':vis'=> 1])
        ->andFilterWhere(['like', 's.name', $filters['name']])
        ->andFilterWhere(['s.procent' => $filters['type']])
        ->orderBy(['s.procent'=>SORT_ASC, 's.value'=>SORT_ASC])
        ->all();

        return !empty($sales) ? $sales : [];
    }

    /**
     * метод создает новую скидку
     * @param string $name
     * @param int    type
     * @param float  $value
     * 
     * @return int
     */
    public static function createSale($name, $type, $value, $base): int
    {
        $model          = new Sale();
        $model->name    = $name;
        $model->procent = $type;
        $model->value   = $value;
        $model->base    = $base;
        if($model->save()) {
            return $model->id;
        } else {
            return 0;
        }
    }
}
