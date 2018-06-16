<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_groupteacherbook".
 *
 * @property integer $id
 * @property integer $calc_book
 * @property integer $calc_groupteacher
 * @property integer $visible
 * @property integer $prime
 */
class Groupteacherbook extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_groupteacherbook';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_book', 'visible', 'calc_groupteacher', 'prime'], 'required'],
            [['calc_book', 'visible', 'calc_groupteacher', 'prime'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_book' => Yii::t('app', 'Book'),
            'calc_groupteacher' => Yii::t('app', 'Group'),
            'visible' => Yii::t('app', 'Visible'),
            'prime' => Yii::t('app', 'Prime'),
        ];
    }
}
