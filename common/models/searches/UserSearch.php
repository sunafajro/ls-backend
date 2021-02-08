<?php

namespace common\models\searches;

use common\models\Role;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Class UserSearch
 * @package school\models\searches
 *
 * @property int $id
 * @property string $username
 * @property string $name
 * @property int $role_id
 * @property string $roleName
 * @property int $visible
 */
class UserSearch extends \yii\base\Model
{
    const ENTITY_CLASS = User::class;
    const ROLE_CLASS = Role::class;

    /** @var int */
    public $id;
    /** @var string */
    public $username;
    /** @var string */
    public $name;
    /** @var int */
    public $role_id;
    /** @var string */
    public $roleName;
    /** @var int */
    public $visible;

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $ut = call_user_func([static::ENTITY_CLASS, 'tableName']);
        $rt = call_user_func([static::ROLE_CLASS, 'tableName']);
        /** @var ActiveQuery $query */
        $query = call_user_func([static::ENTITY_CLASS, 'find']);
        $query->select([
            'id' => "{$ut}.id",
            'username' => "{$ut}.username",
            'name' => "{$ut}.name",
            'roleId' => "{$ut}.role_id",
            'roleName' => "{$rt}.name",
            'visible' => "{$ut}.visible",
        ]);
        $query->leftJoin($rt, "{$rt}.id = {$ut}.role_id");
        $this->load($params);

        if ($this->validate()) {
            $query->andFilterWhere(["{$ut}.id" => $this->id]);
            $query->andFilterWhere(['like',"lower({$ut}.username)", mb_strtolower($this->username)]);
            $query->andFilterWhere(['like',"lower({$ut}.name)", mb_strtolower($this->name)]);
            $query->andFilterWhere(['{$ut}.role_id' => $this->role_id]);
            $query->andFilterWhere(["{$ut}.visible" => $this->visible]);
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
                    'username',
                    'name',
                    'roleName',
                    'visible',
                ],
                'defaultOrder' => [
                    'roleName' => SORT_ASC,
                    'name' => SORT_ASC,
                ],
            ],
        ]);
    }
}