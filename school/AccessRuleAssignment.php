<?php

namespace school;

use school\models\AccessRule;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class AccessRuleAssignment
 * @package school
 *
 * @property integer $id
 * @property string $access_rule_slug
 * @property integer $role_id
 * @property integer $user_id
 *
 * @property-read AccessRule $accessRule
 */
class AccessRuleAssignment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%access_rule_assignments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['access_rule_slug'], 'string'],
            [['role_id', 'user_id'], 'integer'],
            [['access_rule_slug'], 'required'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAccessRule(): ActiveQuery
    {
        return $this->hasOne(AccessRule::class, ['slug' => 'access_rule_slug']);
    }
}