<?php

namespace common\models;

use common\models\queries\BaseUserQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class BaseUser
 * @package common\models
 */
class BaseUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'users';
    }

    /**
     * @return BaseUserQuery|ActiveQuery
     */
    public static function find() : ActiveQuery
    {
        return new BaseUserQuery(get_called_class(), []);
    }

    /**
     * @param array $condition
     * @param bool  $onlyActive
     *
     * @return array
     */
    public static function findUserByCondition(array $condition, bool $onlyActive = true)
    {
        return [];
    }

    /**
     * @param string     $key
     * @param int|string $value
     * @param bool       $onlyActive
     *
     * @return array|null
     */
    public static function findBy(string $key, $value, bool $onlyActive = true)
    {
        return static::findUserByCondition([$key => $value], $onlyActive);
    }
}
