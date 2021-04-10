<?php

namespace school\models\searches;

use school\models\AccessRule;
use school\models\AccessRuleAssignment;
use school\models\User;
use yii\data\ActiveDataProvider;

/**
 * Class AccessRuleAssignmentSearch
 * @package school\models\searches
 *
 * @property string|null $accessRuleName
 * @property string|null $userName
 */
class AccessRuleAssignmentSearch extends AccessRuleAssignment
{
    /** @var string */
    public $accessRuleName;
    /** @var string */
    public $userName;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'role_id'], 'integer'],
            [['access_rule_slug', 'accessRuleName', 'userName'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'access_rule_slug' => \Yii::t('app', 'Access rule slug'),
            'accessRuleName' => \Yii::t('app', 'Access rule'),
            'role_id' => \Yii::t('app', 'Role'),
            'userName' => \Yii::t('app', 'User'),
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $a = self::tableName();
        $r = AccessRule::tableName();
        $u = User::tableName();
        $query = self::find()
            ->select([
                'id' => "{$a}.id",
                'role_id' => "{$a}.role_id",
                'access_rule_slug' => "{$a}.access_rule_slug",
            ])
            ->addSelect([
                'accessRuleName' => "{$r}.name",
                'userName' => "{$u}.name",
            ])
            ->innerJoin($r, "{$r}.slug = {$a}.access_rule_slug")
            ->leftJoin($u, "{$u}.id = {$a}.user_id");
        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(["{$a}.id" => $this->id]);
            $query->andFilterWhere(['like', "lower({$a}.access_rule_slug)", mb_strtolower($this->access_rule_slug)]);
            $query->andFilterWhere(['like', "lower({$r}.name)", mb_strtolower($this->accessRuleName)]);
            $query->andFilterWhere(["{$a}.role_id" => $this->role_id]);
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
                    'access_rule_slug',
                    'accessRuleName',
                    'role_id',
                    'userName',
                ],
            ],
        ]);
    }
}