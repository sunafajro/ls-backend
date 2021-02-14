<?php

namespace school\models\searches;

use school\models\Eduage;
use school\models\Groupteacher;
use school\models\Lang;
use school\models\Office;
use school\models\Service;
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
    /** @var int */
    public $office;
    /** @var int */
    public $age;
    /** @var int */
    public $language;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'office', 'visible', 'age', 'language'], 'integer'],
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
            'service'  => Yii::t('app', 'Service'),
            'schedule' => Yii::t('app', 'Schedule'),
            'office'   => Yii::t('app', 'Office'),
            'age'      => Yii::t('app', 'Age'),
            'language' => Yii::t('app', 'Language'),
            'visible'  => Yii::t('app', 'Status'),
        ];
    }
    public function search(array $params = ['visible' => 1]) : ActiveDataProvider
    {
        $gt = Groupteacher::tableName();
        $st = Service::tableName();
        $ot = Office::tableName();
        $at = Eduage::tableName();
        $lt = Lang::tableName();

        $query = (new \yii\db\Query());
        $query->select([
            'id'         => "$gt.id",
            'teacher_id' => "$gt.calc_teacher",
            'serviceId'  => "$gt.calc_service",
            'service'    => "$st.name",
            'age'        => "$st.calc_eduage",
            'language'   => "$st.calc_lang",
            'level'      => "$gt.calc_edulevel",
            'office'     => "$gt.calc_office",
            'visible'    => "$gt.visible",
        ]);
        $query->from($gt);
        $query->innerJoin($st, "$st.id = $gt.calc_service");
        $query->innerJoin($ot, "$ot.id = $gt.calc_office");
        $query->innerJoin($at, "$at.id = $st.calc_eduage");
        $query->innerJoin($lt, "$lt.id = $st.calc_lang");


        $this->load($params);
        if ($this->visible === '0') {
            $this->visible = 0;
        } else {
            $this->visible = 1;
        }
        if ($this->validate()) {
            $query->andFilterWhere(["{$gt}.id" => $this->id ?? null]);
            $query->andFilterWhere(["{$gt}.visible" => $this->visible ?? null]);
            if ((int)$this->service > 0) {
                $query->andFilterWhere(["{$gt}.calc_service" => $this->service ?? null]);
            } else {
                $query->andFilterWhere(['like', "{$st}.name", $this->service ?? null]);
            }
            $query->andFilterWhere(["{$gt}.calc_office" => $this->office]);
            $query->andFilterWhere(["{$st}.calc_eduage" => $this->age]);
            $query->andFilterWhere(["{$st}.calc_lang" => $this->language]);
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
                        'asc' => ["$gt.id" => SORT_ASC],
                        'desc' => ["$gt.id" => SORT_DESC],
                    ],
                    'visible' => [
                        'asc' => ["$gt.visible" => SORT_ASC],
                        'desc' => ["$gt.visible" => SORT_DESC],
                    ],
                    'serviceId',
                    'service' => [
                        'asc' => ["$st.name" => SORT_ASC],
                        'desc' => ["$st.name" => SORT_DESC],
                    ],
                    'office' => [
                        'asc' => ["$ot.name" => SORT_ASC],
                        'desc' => ["$ot.name" => SORT_DESC],
                    ],
                    'age' => [
                        'asc' => ["$at.name" => SORT_ASC],
                        'desc' => ["$at.name" => SORT_DESC],
                    ],
                    'language' => [
                        'asc' => ["$lt.name" => SORT_ASC],
                        'desc' => ["$lt.name" => SORT_DESC],
                    ]
                ],
                'defaultOrder' => [
                    'service' => SORT_ASC
                ],
            ],
        ]);
    }
}
