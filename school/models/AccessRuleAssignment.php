<?php

namespace school\models;

use school\models\queries\AccessRuleAssignmentQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "access_rule_assignments".
 *
 * @property integer $id
 * @property string $access_rule_slug
 * @property integer $role_id
 * @property integer $user_id
 *
 * @property-read AccessRule $accessRule
 * @property-read Role $role
 * @property-read User $user
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
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'access_rule_slug' => Yii::t('app', 'Access rule slug'),
            'role_id' => Yii::t('app', 'Role'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return AccessRuleAssignmentQuery
     */
    public static function find() : AccessRuleAssignmentQuery
    {
        return new AccessRuleAssignmentQuery(get_called_class(), []);
    }

    /**
     * @return ActiveQuery
     */
    public function getAccessRule(): ActiveQuery
    {
        return $this->hasOne(AccessRule::class, ['id' => 'access_rule_slug']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRole(): ActiveQuery
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}