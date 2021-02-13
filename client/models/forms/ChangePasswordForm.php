<?php

namespace client\models\forms;

use client\models\User;
use Yii;
use yii\base\Model;

/**
 * Class ChangePasswordForm
 * @package client\models
 *
 * @property string $password
 * @property string $password_repeat
 */
class ChangePasswordForm extends Model
{
    /** @var string */
    public $password;
    /** @var string */
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string', 'min' => 8, 'max' => 20],
            ['password_repeat', function ($attribute, $params, $validator) {
                if ($this->password !== $this->$attribute) {
                    $validator->addError($this, $attribute, Yii::t('app', 'Password repeat does not match password'));
                }
            }]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'password_repeat' => Yii::t('app', 'Password repeat'),
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($this->validate()) {
            /** @var User $client */
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