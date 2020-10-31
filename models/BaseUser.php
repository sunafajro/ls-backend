<?php

namespace app\models;

use yii\db\ActiveRecord;

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
     * {@inheritDoc}
     */
    public static function findUserByCondition(array $condition)
    {
        return [];
    }

    /**
     * @param string     $key
     * @param int|string $value
     *
     * @return array|null
     */
    public static function findBy(string $key, $value)
    {
        return static::findUserByCondition([$key => $value]);
    }
}
