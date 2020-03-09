<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_phonebook".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $phonenumber
 * @property string  $description
 * @property integer $visible
 */
class Phonebook extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_phonebook';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['visible'], 'integer'],
            [['name', 'description'], 'string', 'max' => 128],
            [['phonenumber'], 'string', 'max' => 10],
            [['name', 'phonenumber', 'description'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => Yii::t('app', 'Name'),
            'phonenumber' => Yii::t('app', 'Phone'),
            'description' => Yii::t('app', 'Description'),
            'visible'     => 'Visible',
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    /* возвращает список номеров телефонов */
    public static function getPhoneList()
    {
        $phones = (new \yii\db\Query())
        ->select([
            'id'          => 'id',
            'name'        => 'name',
            'phone'       => 'phonenumber',
            'description' => 'description',
        ])
        ->from(self::tableName())
        ->where(['visible' => 1])
        ->all();

        return [
            'columns' => [
                [
                    'id'   => 'id',
                    'name' => '№',
                    'show' => true
                ],
                [
                    'id'   => 'name',
                    'name' => Yii::t('app', 'Name'),
                    'show' => true
                ],
                [
                    'id'   => 'phone',
                    'name' => Yii::t('app','Phone'),
                    'show' => true
                ],
                [
                    'id'   => 'description',
                    'name' => Yii::t('app','Description'),
                    'show' => true
                ]
            ],
            'data'    => $phones
        ];
    }
}
