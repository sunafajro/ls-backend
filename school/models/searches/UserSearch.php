<?php

namespace school\models\searches;

use school\models\User;
use yii\data\ActiveDataProvider;

/**
 * Class UserSearch
 * @package school\models\searches
 */
class UserSearch extends User
{

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            [['id', 'status', 'calc_office', 'visible'], 'integer'],
            [['name', 'login'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params = []): ActiveDataProvider
    {
        $ut = User::tableName();
        $query = User::find();

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(["{$ut}.id" => $this->id]);
            $query->andFilterWhere(['like', "lower({$ut}.name)", mb_strtolower($this->name)]);
            $query->andFilterWhere(['like', "lower({$ut}.login)", mb_strtolower($this->login)]);
            $query->andFilterWhere(["{$ut}.status" => $this->status]);
            $query->andFilterWhere(["{$ut}.calc_office" => $this->calc_office]);
            if (!isset($this->visible)) {
                $this->visible = User::STATUS_ENABLED;
            }
            $query->andFilterWhere(["{$ut}.visible" => $this->visible]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => [
                    'id',
                    'name',
                    'login',
                    'status',
                    'calc_office',
                    'visible',
                ],
                'defaultOrder' => [
                    'status' => SORT_ASC
                ],
            ],
        ]);
    }
}