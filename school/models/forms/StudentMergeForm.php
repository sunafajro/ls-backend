<?php

namespace school\models\forms;

use Yii;
use yii\base\Model;

class StudentMergeForm extends Model
{
    public $id2;

    public function rules()
    {
        return [
            [['id2'], 'required'],
            [['id2'], 'integer'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id2' => Yii::t('app','Student'),
        ];
    }
}