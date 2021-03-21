<?php

namespace school\models\searches;

use school\models\Role;
use yii\data\ActiveDataProvider;

/**
 * Class RoleSearch
 * @package school\models\searches
 */
class RoleSearch extends Role
{
    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params = []): ActiveDataProvider
    {
        $query = Role::find()->active();

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(['id' => $this->id]);
            $query->andFilterWhere(['like', 'name', $this->name]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                ],
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
    }
}