<?php


namespace app\models\search;

use app\models\Groupteacher;
use app\models\Office;
use app\models\Service;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use Yii;

class GroupSearch extends Groupteacher
{
    /** @var int */
    public $id;
    /** @var int */
    public $visible;
    /** @var srtring */
    public $service;
    /** @var srtring */
    public $office;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office', 'visible'], 'integer'],
            [['service'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'teachers' => Yii::t('app', 'Teachers'),
            'service' => Yii::t('app', 'Service'),
            'schedule' => Yii::t('app', 'Schedule'),
            'office'  => Yii::t('app', 'Office'),
            'visible' => Yii::t('app', 'Status'),
        ];
    }
    public function search(array $params = ['visible' => 1]) : ActiveDataProvider
    {
        $groupTable = Groupteacher::tableName();
        $serviceTable = Service::tableName();
        $officeTable = Office::tableName();

        $query = (new \yii\db\Query());
        $query->select([
            'id'      => "$groupTable.id",
            'service' => "$serviceTable.name",
            'office'  => "$groupTable.calc_office",
            'visible' => "$groupTable.visible",
        ]);
        $query->from($groupTable);
        $query->innerJoin($serviceTable, "$serviceTable.id = $groupTable.calc_service");


        $this->load($params);
        if ($this->visible === '0') {
            $this->visible = 0;
        } else {
            $this->visible = 1;
        }
        if ($this->validate()) {
            $query->andFilterWhere(["{$groupTable}.id" => $this->id ?? null]);
            $query->andFilterWhere(["{$groupTable}.visible" => $this->visible ?? null]);
            $query->andFilterWhere(['like', "{$serviceTable}.name", $this->service ?? null]);
            $query->andFilterWhere(["{$groupTable}.calc_office" => $this->office]);
        } else {
            $query->andWhere(new Expression("(0 = 1)"));
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'id' => [
                        'asc' => ["$groupTable.id" => SORT_ASC],
                        'desc' => ["$groupTable.id" => SORT_DESC],
                    ],
                    'visible' => [
                        'asc' => ["$groupTable.visible" => SORT_ASC],
                        'desc' => ["$groupTable.visible" => SORT_DESC],
                    ],
                    'service' => [
                        'asc' => ["$serviceTable.name" => SORT_ASC],
                        'desc' => ["$serviceTable.name" => SORT_DESC],
                    ],
                    'office' => [
                        'asc' => ["$officeTable.name" => SORT_ASC],
                        'desc' => ["$officeTable.name" => SORT_DESC],
                    ]
                ],
                'defaultOrder' => [
                    'service' => SORT_ASC
                ],
            ],
        ]);
    }
}