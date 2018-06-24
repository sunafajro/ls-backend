<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_journalgroup".
 *
 * @property integer $id
 * @property integer $view
 * @property string $data_view
 * @property integer $user_view
 * @property integer $calc_groupteacher
 * @property string $data
 * @property integer $user
 * @property integer $visible
 * @property string $data_visible
 * @property integer $user_visible
 * @property integer $done
 * @property string $data_done
 * @property integer $user_done
 * @property integer $calc_accrual
 * @property string $description
 * @property string $homework
 * @property integer $calc_edutime
 * @property integer $edit
 * @property integer $user_edit
 * @property string $data_edit
 * @property integer $audit
 * @property integer $user_audit
 * @property string $data_audit
 * @property string $description_audit
 * @property integer $calc_teacher
 */
class Journalgroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_journalgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_groupteacher', 'data', 'user', 'visible', 'description', 'homework', 'calc_edutime', 'calc_teacher'], 'required'],
            [['view', 'user_view', 'calc_groupteacher', 'user', 'visible', 'user_visible', 'done', 'user_done', 'calc_accrual', 'calc_edutime', 'edit', 'user_edit', 'audit', 'user_audit', 'calc_teacher'], 'integer'],
            [['data_view', 'data', 'data_visible', 'data_done', 'data_edit', 'data_audit'], 'safe'],
            [['description', 'homework', 'description_audit'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'view' => Yii::t('app', 'View'),
            'data_view' => Yii::t('app', 'Data View'),
            'user_view' => Yii::t('app', 'User View'),
            'calc_groupteacher' => Yii::t('app', 'Calc Groupteacher'),
            'data' => Yii::t('app', 'Date'),
            'user' => Yii::t('app', 'User'),
            'visible' => Yii::t('app', 'Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'done' => Yii::t('app', 'Done'),
            'data_done' => Yii::t('app', 'Data Done'),
            'user_done' => Yii::t('app', 'User Done'),
            'calc_accrual' => Yii::t('app', 'Calc Accrual'),
            'description' => Yii::t('app', 'Lesson description'),
            'homework' => Yii::t('app', 'Homework'),
            'calc_edutime' => Yii::t('app', 'Time'),
            'edit' => Yii::t('app', 'Edit'),
            'user_edit' => Yii::t('app', 'User Edit'),
            'data_edit' => Yii::t('app', 'Data Edit'),
            'audit' => Yii::t('app', 'Audit'),
            'user_audit' => Yii::t('app', 'User Audit'),
            'data_audit' => Yii::t('app', 'Data Audit'),
            'description_audit' => Yii::t('app', 'Description Audit'),
            'calc_teacher' => Yii::t('app', 'Teacher'),
        ];
    }
}
