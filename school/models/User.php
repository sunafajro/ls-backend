<?php

namespace school\models;

use common\models\BaseUser;
use school\School;
use school\widgets\userInfo\UserInfoWidget;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $site        @deprecated использовалось для доступа к сайту
 * @property integer $visible
 * @property string  $login
 * @property string  $pass
 * @property string  $name
 * @property integer $status
 * @property integer $calc_office
 * @property integer $calc_teacher
 * @property integer $calc_city
 * @property string  $logo
 * @property string  $module_type
 *
 * @property-read City    $city
 * @property-read Office  $office
 * @property-read Role    $role
 * @property-read Teacher $teacher
 * @property-read UserImage $image
 */
class User extends BaseUser
{
    /** @deprecated Убрать. Реализовать в форме UserForm */
    public $pass_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['calc_city', 'calc_office', 'calc_teacher', 'site'], 'default', 'value' => 0],
            [['module_type'],  'default', 'value' => School::MODULE_NAME],
            [['site', 'visible', 'status', 'calc_office', 'calc_teacher', 'calc_city'], 'integer'],
            [['login', 'pass', 'name', 'logo', 'module_type'], 'string'],
            [['login'], 'unique', 'on' => 'create',
                'when' => function ($model) {
                    return self::findBy(self::tableName() . '.login', $model->login) !== NULL;
                }],
            [['login'], 'unique', 'on' => 'update', 
                'when' => function ($model) {
                    return self::getUserName($model->id) !== $model->login;
                }],
            [['login', 'name'], 'string', 'min' => 3],
            [['pass'], 'string', 'min' => 8],
            [['visible', 'login', 'pass', 'name', 'status', 'module_type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id'           => 'ID',
            'site'         => 'Site',
            'visible'      => Yii::t('app','Status'),
            'login'        => Yii::t('app','User login'),
            'pass'         => Yii::t('app','Password'),
            'name'         => Yii::t('app','Full name'),
            'status'       => Yii::t('app','Role'),
            'calc_office'  => Yii::t('app','Office'),
            'calc_teacher' => Yii::t('app','Teacher'),
            'calc_city'    => Yii::t('app','City'),
            'logo'         => Yii::t('app','User logo'),
            'module_type'  => Yii::t('app','Module name'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function delete()
    {
        $this->visible = 0;

        return $this->save(true, ['visible']);
    }

    /**
     * @return ActiveQuery
     */
    public static function find(): ActiveQuery
    {
        $query = parent::find();
        return $query->andWhere(['users.module_type' => School::MODULE_NAME]);
    }

    /**
     * {@inheritDoc}
     */
    public static function findUserByCondition(array $condition, bool $onlyActive = true)
    {
        $userData = self::find()
            ->select([
                'id'         => 'users.id',
                'username'   => 'users.login',
                'password'   => 'users.pass',
                'fullName'   => 'users.name',
                'roleId'     => 'roles.id',
                'roleName'   => 'roles.name',
                'teacherId'  => 'users.calc_teacher',
                'officeId'   => 'offices.id',
                'officeName' => 'offices.name',
                'cityId'     => 'cities.id',
                'cityName'   => 'cities.name',
            ])
            ->innerJoin(['roles'  => Role::tableName()], "roles.id = users.status")
            ->leftJoin(['offices' => Office::tableName()], "offices.id = users.calc_office")
            ->leftJoin(['cities'  => City::tableName()], "cities.id = offices.calc_city")
            ->andWhere([
                'users.visible' => 1,
            ])
            ->andWhere($condition)
            ->asArray()
            ->one();

        // принудительное приведение типов
        foreach ($userData ?? [] as $key => $value) {
            if (in_array($key, ['id', 'roleId', 'teacherId', 'officeId', 'cityId']) && !empty($value)) {
                $userData[$key] = intval($value);
            }
        }

        return $userData;
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getUserName(int $id): ?string
    {
        return self::findBy(self::tableName() . '.id', $id)['username'] ?? null;
    }

    /**
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'calc_city']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffice(): ActiveQuery
    {
        return $this->hasOne(Office::class, ['id' => 'calc_office']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRole(): ActiveQuery
    {
        return $this->hasOne(Role::class, ['id' => 'status']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTeacher(): ActiveQuery
    {
        return $this->hasOne(Teacher::class, ['id' => 'calc_teacher']);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getUserInfoBlock() : string
    {
        return UserInfoWidget::widget();
    }

    /**
     * возвращает данные пользователя, для информационного блока
     * 
     * @return array
     */
    public static function getUserInfo() : array
    {
        $userData = [];

        $userData['name'] = Yii::$app->user->identity->fullName;
        if (Yii::$app->user->identity->teacherId) {
            $userData['teacherId'] = Yii::$app->user->identity->teacherId;
        } else {
            $userData['teacherId'] = null;
        }
        $userData['roleId'] = Yii::$app->user->identity->roleId;
        $userData['role'] = Yii::$app->user->identity->roleName;
        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
            $userData['office'] = Yii::$app->user->identity->officeName;
            $userData['officeId'] = Yii::$app->user->identity->officeId;
        } else {
            $userData['office'] = null;
        }

        return $userData;
    }

    /**
     * @return ActiveQuery
     */
    public function getImage(): ActiveQuery
    {
        return $this->hasOne(UserImage::class, ['entity_id' => 'id'])->andWhere(['entity_type' => UserImage::TYPE_USER_IMAGE]);
    }

    /**
     * @return string|null
     */
    public function getImageWebPath(): ?string
    {
        if (($image = $this->image) !== null) {
            return "/user/download-image/{$this->id}";
        } else if (!empty($this->logo)) {
            $imagePath = "user/{$this->id}/logo/{$this->logo}";
            $image = Yii::getAlias("@uploads/{$imagePath}");
            if (file_exists($image)) {
                return "/uploads/{$imagePath}";
            }
        }

        return Yii::getAlias('@web/images/dream.jpg');
    }

    /**
     * Метод проверки ролей пользователей на доступ к ресурсам
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public static function checkAccess(string $controller, string $action) : bool
    {
        $result = true;
        $roleId = Yii::$app->user->identity->roleId;

        switch ($controller) {

            case 'studname':
                if (in_array($action, [
                    'update', 'active', 'inactive',
                    'detail', 'change-office', 'update-debt',
                    'settings', 'update-settings', 'successes'])) {
                    switch($roleId) {
                        case 3:
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    $result = false;
                }
                return $result;

            case 'invoice':
                if (in_array($action, ['index','create','toggle','get-data'])) {
                    switch($roleId) {
                        case 3:
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else if ($action === 'delete') {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    $result = false;
                }
                return $result;

            default: $result = false;
		}
		return $result;
	}
}
