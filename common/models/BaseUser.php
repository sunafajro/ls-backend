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
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

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

    /**
     * @return array
     */
    public static function getStatusLabels(): array
    {
        return [
            static::STATUS_ENABLED => \Yii::t('app', 'Enabled'),
            static::STATUS_DISABLED => \Yii::t('app', 'Disabled'),
        ];
    }
}
