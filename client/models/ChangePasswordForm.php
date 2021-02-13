<?php

namespace client\models;

use Yii;
use yii\base\Model;
use client\models\User;

/**
 * Class ChangePasswordForm
 * @package client\models
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string', 'min' => 8, 'max' => 20],
            ['password_repeat', function ($attribute, $params, $validator) {
                if ($this->password !== $this[$attribute]) {
                    $validator->addError($this, $attribute, Yii::t('app', 'Password repeat does not match password'));
                }
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Password repeat'),
        ];
    }

    public function save()
    {
        if ($this->validate()) {
            $client = User::find()->where([
                'calc_studname' => Yii::$app->user->id
            ])->one();
            if ($client) {
                $client->password = md5($this->password);
                if ($client->save()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}