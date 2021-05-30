<?php

namespace school\models;

use school\models\queries\AccessRuleQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "access_rules".
 *
 * @property integer $id
 * @property string $slug
 * @property string $name
 * @property string $description
 *
 * @property-read AccessRuleAssignment[] $assignments
 * @property-read Role $role
 * @property-read User $user
 */
class AccessRule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%access_rules}}';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['slug', 'name'], 'string'],
            [['slug'], 'unique'],
            [['slug', 'name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'slug'        => Yii::t('app', 'Slug'),
            'name'        => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return AccessRuleQuery
     */
    public static function find() : AccessRuleQuery
    {
        return new AccessRuleQuery(get_called_class(), []);
    }

    /**
     * @param string $controller
     * @return array
     */
    public static function getCRUD(string $controller): array
    {
        return [
            'create' => self::checkAccess("{$controller}_create"),
            'update' => self::checkAccess("{$controller}_update"),
            'delete' => self::checkAccess("{$controller}_delete"),
        ];
    }

    /**
     * @param string $slug
     * @return bool
     */
    public static function checkAccess(string $slug): bool
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;

        return AccessRuleAssignment::find()->where([
                'access_rule_slug' => $slug,
            ])
            ->andWhere([
                'or',
                ['role_id' => $auth->roleId],
                ['user_id' => $auth->id],
                ['all' => 1],
            ])
            ->cache(60 * 60)
            ->exists();
    }

    /**
     * @return ActiveQuery
     */
    public function getAssignments(): ActiveQuery
    {
        return $this->hasMany(AccessRuleAssignment::class, ['slug' => 'access_rule_slug']);
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