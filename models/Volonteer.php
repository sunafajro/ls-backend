<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "volonteers".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $birthdate
 * @property string  $city
 * @property string  $occupation
 * @property string  $type
 * @property string  $social
 * @property string  $phone
 * @property string  $note
 */

class Volonteer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'volonteers';
    }

        /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['visible'],   'integer'],
            [['birthdate'], 'safe'],
            [['name', 'city', 'occupation', 'type', 'social', 'phone', 'note'], 'string'],
            [['name', 'phone', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'name'       => Yii::t('app', 'Name'),
            'birthdate'  => Yii::t('app', 'Birthdate'),
            'city'       => Yii::t('app', 'City'),
            'occupation' => Yii::t('app', 'Occupation'),
            'type'       => Yii::t('app', 'Type'),
            'social'     => Yii::t('app', 'Social'),
            'phone'      => Yii::t('app', 'Phone'),
            'note'       => Yii::t('app', 'Note'),
            'visible'    => Yii::t('app', 'Visible'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getVolonteers()
    {
        $data = (new \yii\db\Query())
        ->select([
            'id'         => 'id',
            'name'       => 'name',
            'birthdate'  => 'birthdate',
            'city'       => 'city',
            'occupation' => 'occupation',
            'type'       => 'type',
            'social'     => 'social',
            'phone'      => 'phone',
            'note'       => 'note',
        ])
        ->from(self::tableName())
        ->where(['visible' => 1])
        ->orderby(['name' => SORT_ASC])
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
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Birthdate'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'City'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Occupation'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Type'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Social'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Phone'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Note'),
                    'show' => true
                ],
            ],
            'data'    => $data
        ];
    }
}