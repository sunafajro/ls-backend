<?php

namespace school\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calc_cabinetoffice".
 *
 * @property integer $id
 * @property string  $name
 * @property integer $calc_office
 * @property integer $visible
 */
class Room extends ActiveRecord
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
            [['visible'], 'default', 'value' => 1],
            [['calc_office', 'visible'], 'integer'],
            [['name'], 'string', 'max' => 256],
            [['name', 'calc_office', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'name'        => Yii::t('app', 'Name'),
            'calc_office' => Yii::t('app', 'Office'),
            'visible'     => Yii::t('app', 'Visible'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getRoomsList()
    {
        $rooms = (new \yii\db\Query())
        ->select([
            'id'        => 'r.id',
            'name'      => 'r.name',
            'office_id' => 'o.id',
            'office'    => 'o.name'
        ])
        ->from(['r' => self::tableName()])
        ->innerJoin(['o' => Office::tableName()], 'o.id = r.calc_office')
        ->where(['r.visible' => 1])
        ->orderBy([
            'o.name' => SORT_ASC,
            'r.name' => SORT_ASC,
        ])
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

    public function getRooms($oid)
    {
    	$data = (new \yii\db\Query())
    	->select('id as id, name as name')
        ->from(self::tableName())
        ->andFilterWhere(['calc_office' => $oid])
    	->orderBy(['name'=>SORT_ASC])
        ->all();        
        return $data;
    }
}