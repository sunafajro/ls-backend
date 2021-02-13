<?php

namespace school\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

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
class Edunormteacher extends ActiveRecord
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
            'id'               => 'ID',
            'calc_teacher'     => 'Calc Teacher',
            'calc_edunorm'     => Yii::t('app', 'Hourly tax'),
            'calc_edunorm_day' => Yii::t('app', 'Daily tax'),
            'data'             => 'Data',
            'visible'          => 'Visible',
            'active'           => 'Active',
            'company'          => Yii::t('app','Job place')
        ];
    }

    /**
     * Возвращает массив ставок преподавателя и корпоративную надбавку
     * @param int $tid
     *
     * @return array
     */
    public static function getTeacherTaxesForAccrual(int $tid) : array
    {
        /** @var Edunorm[] $eduNorms */
        return (new Query())
            ->select(['id' => 'ent.id', 'value' => 'en.value', 'company' => 'ent.company'])
            ->from(['en' => Edunorm::tableName()])
            ->innerJoin(['ent' => self::tableName()],'ent.calc_edunorm = en.id')
            ->where([
                'ent.calc_teacher' => $tid,
                'ent.active'       => 1,
            ])
            ->indexBy('company')
            ->all();
    }

    /**
     * @param int[] $teachers
     *
     * @return array
     */
    public static function getTaxes(array $teachers) : array
    {
        return (new Query())
        ->select([
            'id'    => 'en.id',
            'entId' => 'ent.id',
            'name'  => 'en.name',
            'value' => 'en.value'
        ])
        ->from(['en' => Edunorm::tableName()])
        ->innerJoin(['ent' => self::tableName()], 'ent.calc_edunorm = en.id')
        ->andFilterwhere(['in', 'ent.calc_teacher', $teachers])
        ->all();
    }
}
