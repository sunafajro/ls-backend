<?php

namespace common\models;

use common\models\queries\UserQuery;
use yii\db\ActiveQuery;

/**
 * Class User
 * @package common\models
 * @property string $pass
 */
class User extends ActiveRecord
{
    const DEFAULT_FIND_CLASS = UserQuery::class;
    const DEFAULT_MODULE_TYPE = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%users}}';
    }

    /**
     * @return UserQuery|ActiveQuery
     */
    public static function find(): ActiveQuery
    {
        $findClass = static::DEFAULT_FIND_CLASS;
        $findQuery = new $findClass(get_called_class(), []);

        return static::addDefaultFindCondition($findQuery);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // переделать под использование более строгих алгоритмов
        $this->pass = md5($this->pass);
        return true;
    }

    /**
     * @param array $condition
     * @param bool $onlyActive
     *
     * @return array|null
     */
    public static function findUserByCondition(array $condition, bool $onlyActive = true): ?array
    {
        return [];
    }

    /**
     * @param string $key
     * @param int|string $value
     * @param bool $onlyActive
     *
     * @return array|null
     */
    public static function findBy(string $key, $value, bool $onlyActive = true): ?array
    {
        return static::findUserByCondition([$key => $value], $onlyActive);
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getUserName(int $id): ?string
    {
        return self::findBy(static::tableName() . '.id', $id)['username'] ?? null;
    }

    /**
     * @param UserQuery $query
     * @return UserQuery
     */
    public static function addDefaultFindCondition(UserQuery $query): UserQuery
    {
        if (static::DEFAULT_MODULE_TYPE) {
            $query->byModuleType(static::DEFAULT_MODULE_TYPE);
        }
        return $query;
    }
}