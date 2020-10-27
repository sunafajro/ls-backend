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
}
