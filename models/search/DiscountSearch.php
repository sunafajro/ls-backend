<?php

namespace app\models\search;

use app\models\Sale;
use yii\data\ActiveDataProvider;

class DiscountSearch extends Sale
{
    public function rules()
    {
        return [
            [['id', 'procent'], 'integer'],
            [['name'],  'string'],
            [['value'], 'number'],
        ];
    }

    public function search(array $params = [])
    {
        $dt = Sale::tableName();

        $query = (new \yii\db\Query());
        $query->select([
            'id'      => 'd.id',
            'name'    => 'd.name',
            'value'   => 'd.value',
            'procent' => 'd.procent',
            'data'    => 'd.data',
            'base'    => 'd.base',
        ]);
        $query->from(['d' => $dt]);

        $query->where([
            'd.visible'  => 1,
        ]);

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(['like', 'd.name', $this->name]);
            $query->andFilterWhere(['d.value' => $this->value]);
            $query->andFilterWhere(['d.procent' => $this->procent]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'id',
                    'name',
                    'value',
                    'procent',
                    'data',
                ],
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
            ],
        ]);
    }
}