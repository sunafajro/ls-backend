<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_cabinetoffice".
 *
 * @property integer $id
 * @property string $name
 * @property integer $calc_office
 * @property integer $visible
 */
class Room extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_cabinetoffice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'calc_office', 'visible'], 'required'],
            [['calc_office', 'visible'], 'integer'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'calc_office' => Yii::t('app', 'Office'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    public static function getRoomsList()
    {
        /* получаем список доступных кабинетов */
        $rooms = (new \yii\db\Query())
        ->select('r.id as id, r.name as name, o.id as office_id, o.name as office')
        ->from('calc_cabinetoffice r')
        ->innerJoin('calc_office o', 'o.id=r.calc_office')
        ->where('r.visible=:vis', [':vis'=>1])
        ->orderBy(['o.name'=>SORT_ASC, 'r.name'=>SORT_ASC])
        ->all();
        /* получаем список доступных кабинетов */
        
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
                    'id'   => 'office_id',
                    'name' => Yii::t('app', 'Office ID'),
                    'show' => false
                ],
                [
                    'id'   => 'office',
                    'name' => Yii::t('app', 'Office'),
                    'show' => true
                ]
            ],
            'data'    => $rooms
        ];
    }
}