<?php

namespace client\models;

use Yii;

/**
 * This is the model class for table "tbl_client_access".
 *
 * @property integer $id
 * @property integer $site
 * @property string  $username
 * @property string  $password
 * @property integer $calc_studname
 * @property string  $access_token
 * @property string  $date
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{tbl_client_access}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['site', 'calc_studname'], 'integer'],
            [['username', 'password', 'calc_studname', 'date'], 'required'],
            [['username', 'password'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'site' => 'Site',
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'calc_studname' => Yii::t('app', 'Student'),
            'date' => Yii::t('app', 'Date'),
        ];
    }

    /**
     * Resets access token
     * @throws \yii\base\Exception
     */
    public function resetAccessToken(): bool
    {
        $this->access_token = Yii::$app->security->generateRandomString() . '_' . time();
        return $this->save(true, ['access_token']);
    }
}
