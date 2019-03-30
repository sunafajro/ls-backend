<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_edunormteacher".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_edunorm
 * @property integer $calc_edunorm_day
 * @property string $data
 * @property integer $visible
 * @property integer $active
 * @property integer $company 
 */
class Edunormteacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_edunormteacher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_edunorm', 'data', 'visible', 'active', 'company'], 'required'],
            [['calc_teacher', 'calc_edunorm', 'calc_edunorm_day', 'visible', 'active', 'company'], 'integer'],
            [['data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calc_teacher' => 'Calc Teacher',
            'calc_edunorm' => Yii::t('app', 'Hourly tax'),
            'calc_edunorm_day' => Yii::t('app', 'Daily tax'),
            'data' => 'Data',
            'visible' => 'Visible',
            'active' => 'Active',
            'company' => Yii::t('app','Job place')
        ];
    }

    /* возвращает массив ставок преподавателя и корпоративную надбавку */
    public static function getTeacherTaxesForAccrual($tid)
    {
      	$edunorm = (new \yii\db\Query())
      	->select('ent.id as entid, en.value as value, t.value_corp as corp, ent.company as tjplace')
      	->from('calc_edunorm en')
      	->leftJoin('calc_edunormteacher ent','ent.calc_edunorm=en.id')
      	->leftJoin('calc_teacher t', 'ent.calc_teacher=t.id')
      	->where('ent.calc_teacher=:tid AND ent.active=:one AND ent.visible=:one', [':tid' => $tid, ':one' => 1])
        ->all();

        if (!empty($edunorm)) {
          /* готовим ставки преподавателей */
          $normid = [];
          $norm = [];
          $corp = 0;
          foreach ($edunorm as $en) {
            /* создаем массив ставок */
            $normid[$en['tjplace']] = $en['entid'];
            $norm[$en['tjplace']] = $en['value'];
            $corp = $en['corp'];
          }
          
         return ['norm' => $norm, 'corp' => $corp, 'normid' => $normid];
        }
        
        return $edunorm;
    }

    public static function getTaxes($teachers)
    {
        $edunorms = (new \yii\db\Query())
        ->select([
            'id' => 'en.id',
            'entId' => 'ent.id',
            'name' => 'en.name',
            'value' => 'en.value'
        ])
        ->from(['en' => 'calc_edunorm'])
        ->innerJoin(['ent' => self::tableName()], 'ent.calc_edunorm = en.id')
        ->andFilterwhere(['in', 'ent.calc_teacher', $teachers])
        ->all();
        return $edunorms;
    }
}
