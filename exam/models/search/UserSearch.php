<?php


namespace exam\models\searches;

use exam\models\User;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'login'], 'string'],
        ];
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params = []) : ActiveDataProvider
    {
        $ut = User::tableName();

        $query = User::find()->active();

        $this->load($params);

        if ($this->validate()) {
            $query->andFilterWhere(["{$ut}.id" => $this->id]);
            $query->andFilterWhere(['like', "lower({$ut}.name)", mb_strtolower($this->name)]);
            $query->andFilterWhere(['like', "lower({$ut}.login)", mb_strtolower($this->login)]);
            $query->andFilterWhere(["{$ut}.status" => $this->status]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => [
                    'id',
                    'name',
                ],
                'defaultOrder' => [
                    'name' => SORT_DESC
                ],
            ],
        ]);
    }
}