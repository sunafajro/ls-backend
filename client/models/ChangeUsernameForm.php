<?php

namespace client\models;

use Yii;
use yii\base\Model;
use client\models\User;

/**
 * Class ChangeUsernameForm
 * @package client\models
 */
class ChangeUsernameForm extends Model
{
    public $username;
    public $username_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'username_repeat'], 'required'],
            ['username', 'match', 'pattern' => '#^[a-zA-Z0-9_\.-]+$#', 'message' => Yii::t('app', 'Username contains restricted symbols')],
            ['username', 'string', 'min' => 5, 'max' => 20],
            ['username', function($attribute, $params, $validator) {
                $student = (new \yii\db\Query())
                ->select('id')
                ->from(['ca' => 'tbl_client_access'])
                ->where([
                    'username' => $this[$attribute]
                ])
                ->andWhere(['!=', 'calc_studname', Yii::$app->user->id])
                ->one();
                if ($student) {
                    $validator->addError($this, $attribute, Yii::t('app', 'Username {value} is already in use'));
                }
            }],
            ['username_repeat', function ($attribute, $params, $validator) {
                if ($this->username !== $this[$attribute]) {
                    $validator->addError($this, $attribute, Yii::t('app', 'Username repeat does not match username'));
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
            'username' => Yii::t('app', 'Username'),
            'username_repeat' => Yii::t('app', 'Username repeat'),
        ];
    }

    public function save()
    {
        if ($this->validate()) {
            $client = User::find()->where([
                'calc_studname' => Yii::$app->user->id
            ])->one();
            if ($client) {
                $client->username = $this->username;
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