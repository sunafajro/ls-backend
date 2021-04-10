<?php

namespace school\models\searches;

use school\models\AccessRule;
use yii\data\ActiveDataProvider;

/**
 * Class AccessRuleSearch
 * @package school\models\searches
 */
class AccessRuleSearch extends AccessRule
{
    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['slug', 'name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $ar = self::tableName();
        $query = self::find();
        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(["id" => $this->id]);
            $query->andFilterWhere(["like", "slug", mb_strtolower($this->slug)]);
            $query->andFilterWhere(["like", "name", mb_strtolower($this->name)]);
        } else {
            $query->where('0 = 1');
        }
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' =>["id" => SORT_ASC],
                'attributes' => [
                    'id',
                    'slug',
                    'name',
                ],
            ],
        ]);
    }
}