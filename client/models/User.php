<?php

namespace client\models;

use Yii;

/**
 * This is the model class for table "tbl_client_access".
 *
 * @property integer $id
 * @property integer $site
 * @property string $username
 * @property string $password
 * @property integer $calc_studname
 * @property string $date
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_client_access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
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
    public function attributeLabels()
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
}
