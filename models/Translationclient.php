<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_translationclient".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $contact
 * @property string $address
 * @property string $description
 * @property integer $user
 * @property string $data
 * @property integer $visible
 * @property integer $user_visible
 * @property string $data_visible
 */
class Translationclient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_translationclient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'user', 'data', 'visible'], 'required'],
            [['description'], 'string'],
            [['user', 'visible', 'user_visible'], 'integer'],
            [['data', 'data_visible'], 'safe'],
            [['name', 'phone', 'email', 'contact', 'address'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Nomination'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'contact' => Yii::t('app', 'Contact pearson'),
            'address' => Yii::t('app', 'Address'),
            'description' => Yii::t('app', 'Description'),
            'user' => Yii::t('app', 'User'),
            'data' => Yii::t('app', 'Data'),
            'visible' => Yii::t('app', 'Visible'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'data_visible' => Yii::t('app', 'Data Visible'),
        ];
    }
    
    /**
     *  Метод получает список клиентов и формирует из него одномерный массив
     */
    public static function getClientListSimple()
    {
        $clients = [];
        $clients_obj = self::find()->select(['id'=>'id', 'name'=>'name'])->where('visible=:one', [':one' => 1])->orderby(['name'=>SORT_ASC])->all();
        if($clients_obj !== NULL) {
            foreach($clients_obj as $c) {
                $clients[$c->id] = $c->name;
            }
        }
        return $clients;
    }
    
    public static function getClientList($params)
    {
        $clients = (new \yii\db\Query())
        ->select('id as id, name as name, address as address, contact as contact, phone as phone, email as email, description as description')
        ->from('calc_translationclient')
        ->where('visible=:vis', [':vis'=>1])
        ->andFilterWhere(['like', 'name', $params['TSS']])
        ->orderby(['name'=>SORT_ASC])
        ->all();
        
        return $clients;
    }
}
