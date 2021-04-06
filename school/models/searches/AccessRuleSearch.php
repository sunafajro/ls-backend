<?php

namespace school\models\searches;

use school\models\AccessRule;
use school\models\User;
use yii\data\ActiveDataProvider;

/**
 * Class AccessRuleSearch
 * @package school\models\searches
 */
class AccessRuleSearch extends AccessRule
{
    /** @var string */
    public $userName;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'role_id'], 'integer'],
            [['controller', 'action', 'userName'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'controller' => \Yii::t('app', 'Controller'),
            'action'     => \Yii::t('app', 'Action'),
            'role_id'    => \Yii::t('app', 'Role'),
            'userName'    => \Yii::t('app', 'User'),
        ];
    }

    public function search(array $params)
    {
        $ar = AccessRuleSearch::tableName();
        $u = User::tableName();
        $query = AccessRuleSearch::find()
            ->select([
                'id' => "{$ar}.id",
                'controller' => "{$ar}.controller",
                'action' => "{$ar}.action",
                'role_id' => "{$ar}.role_id",
            ])
            ->addSelect(['userName' => "{$u}.name"])
            ->leftJoin(User::tableName(), "{$u}.id = {$ar}.user_id");
        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(["{$ar}.id" => $this->id]);
            $query->andFilterWhere(["{$ar}.controller" => $this->controller]);
            $query->andFilterWhere(["{$ar}.action" => $this->action]);
            $query->andFilterWhere(["{$ar}.role_id" => $this->role_id]);
            $query->andFilterWhere(['like', "lower({$u}.name)", mb_strtolower($this->userName)]);
        } else {
            $query->where('0 = 1');
        }
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' =>["id" => SORT_ASC],
                'attributes' => [
                    'id',
                    'controller',
                    'action',
                    'role_id',
                    'userName',
                ],
            ],
        ]);
    }
}