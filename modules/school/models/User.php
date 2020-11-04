<?php

namespace app\modules\school\models;

use app\models\BaseUser;
use app\models\City;
use app\models\Office;
use app\models\Teacher;
use app\modules\school\School;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Html;

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
 * @property City    $city
 * @property Office  $office
 * @property Role    $role
 * @property Teacher $teacher
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
            [['visible'],      'default', 'value' => 1],
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
            'visible'      => Yii::t('app','Active'),
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
     * {@inheritDoc}
     */
    public static function findUserByCondition(array $condition, bool $onlyActive = true)
    {
        return self::find()
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
            ->where([
                'users.visible'     => 1,
                'users.module_type' => School::MODULE_NAME,
            ])
            ->andWhere($condition)
            ->asArray()
            ->one();
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getUserName(int $id)
    {
        return self::findBy(self::tableName() . '.id', $id)['username'] ?? null;
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'calc_city']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'calc_office']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'status']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::class, ['id' => 'calc_teacher']);
    }
    
    /**
     * 
     * Метод генерирует html блок с краткой информацией о текущем пользователе.
     * Блок размещается в боковое меню.
     * 
     * @return string
     */
    public static function getUserInfoBlock()
    {
        $array = [];
        $array[] = Html::beginTag('div', ['class' => 'well well-sm small']);
		$array[] = Html::tag('b', Yii::$app->user->identity->fullName);
        if (Yii::$app->user->identity->teacherId) {
            $array[] = Html::a('', ['teacher/view', 'id' => Yii::$app->user->identity->teacherId], ['class'=>'fa fa-user btn btn-default btn-xs']);
        }            
        $array[] = Html::tag('br');
        $array[] = Html::tag('i', Yii::$app->user->identity->roleName);
        if (Yii::$app->user->identity->roleId === 4) {
            $array[] = Html::tag('br');
            $array[] = Yii::$app->user->identity->officeName;
        }
        $array[] = Html::endTag('div');
        
        return join('', $array);
    }

    /**
     * возвращает данные пользователя, для информационного блока
     * 
     * @return array
     */
    public static function getUserInfo()
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

    /* метод генерирует html блок с информацией о текущем пользователе */

    /**
     * Метод возвращает список пользователей. Выдача фильтруется
     * в зависимости от содержимого массива $params.
     * active: 1 - активные, 2 - неактивные, NULL - все.
     * role: роль пользователей.
     * @param array $params
     * @return array
     */
    public static function getUserListFiltered(array $params) : array
    {
        $tbl = self::tableName();
        return self::find()
	        ->select([
                'id'      => "{$tbl}.id",
                'name'    => "{$tbl}.name",
                'login'   => "{$tbl}.login",
                'roleId'  => "{$tbl}.status",
                'role'    => 'r.name',
                'office'  => 'o.name',
                'visible' => "{$tbl}.visible"
            ])
            ->leftJoin(['r' => Role::tableName()], "{$tbl}.status = r.id")
            ->leftJoin(['o' => Office::tableName()], "{$tbl}.calc_office = o.id")
            ->andWhere(["{$tbl}.module_type"   => School::MODULE_NAME])
	        ->andFilterWhere(["{$tbl}.visible" => $params['active'] ?? null])
            ->andFilterWhere(["{$tbl}.status"  => $params['role'] ?? null])
	        ->orderBy(['r.id' => SORT_ASC, "{$tbl}.name" => SORT_ASC])
            ->asArray()
	        ->all();
    }

    /**
     * Возвращает информацию по одному пользователю.
     * @param integer $id
     * @return mixed
     */
    public static function getUserInfoById($id)
    {
        return (new Query())
            ->select([
                'uid'     => 'u.id',
                'uname'   => 'u.name',
                'ulogin'  => 'u.login',
                'vis'     => 'u.visible',
                'urole'   => 'r.name',
                'uoffice' => 'o.name',
                'ulogo'   => 'u.logo'
            ])
            ->from(['u' => User::tableName()])
            ->leftjoin(['r' => Role::tableName()], 'r.id = u.status')
            ->leftjoin(['o' => Office::tableName()], 'o.id = u.calc_office')
            ->where(['u.id' => $id])
            ->one();
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
            #region Клиенты
            case 'studname':
                if (in_array($action, ['view', 'index'])) {
                    switch($roleId) {
                        case 3:
                        case 4:
                        case 5:
                        case 6: $result = true; break;
                        default: $result = false;
                    }
                } else if (in_array($action, [
                    'update', 'active', 'inactive',
                    'detail', 'change-office', 'update-debt',
                    'settings', 'update-settings', 'successes'])) {
                    switch($roleId) {
                        case 3:
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else if (in_array($action, ['merge', 'delete'])) {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else if ($action === 'offices') {
                    switch($roleId) {
                        case 3:
                        case 4:
                        case 8:
                        case 11: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    $result = false;
                }
                return $result;
            #endregion
            /* подраздел счета клиента */
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
            /* подраздел счета клиента */

            /* подраздел скидки клиента */
            case 'salestud':
                if ($action === 'approve' || $action === 'disable-all' || $action === 'index') {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else if($action === 'create' || $action === 'disable' || $action === 'enable' || $action === 'autocomplete') {
                    switch($roleId) {
                        case 3:
                        case 4: $result = true; break;
                        default:$result = false;
                    }
                } else {
                    $result = false;
                }
                break;
            /* подраздел скидки клиента */

            /* подраздел аттестации клиента */
            case 'student-grade':
                if ($action === 'index') {
                    switch($roleId) {
                        case 3:
                        case 4:
                        case 5: $result = true; break;
                        default: $result = false;
                    }
                }
                if ($action === 'create' || $action === 'delete' || $action = 'download-attestation') {
                    switch($roleId) {
                        case 3:
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            /* подраздел аттестации клиента */
            /* раздел Клиенты */

            #region Расписание */
            case 'schedule':
                if ($action === 'app-actions' ||
                    $action === 'app-create' ||
                    $action === 'app-delete' ||
                    $action === 'app-filters' ||
                    $action === 'app-groups' ||
                    $action === 'app-hours' ||
                    $action === 'app-lessons' ||
                    $action === 'app-offices' ||
                    $action === 'app-rooms' ||
                    $action === 'app-teachers' ||
                    $action === 'app-update' ||
                    $action === 'index') {
                    switch($roleId) {
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 10: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            #endregion

            #region Переводы
            case 'langtranslator':
            case 'translate':
            case 'translation':
			case 'translationclient':
            case 'translationlang':
            case 'translationnorm':
            case 'translator':
                switch($roleId) {
                    case 3:
                    case 9: $result = true; break;
                    default: $result = false;        
                }
                break;
            #endregion

            #region Скидки
            case 'sale':
                if ($action === 'create' || $action === 'update' || $action === 'delete') {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else if($action === 'index') {
                    switch($roleId) {
                        case 3:
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    $result = false;
                }
                break;
            #endregion

            #region Справочники
            case 'reference':
                if ($action === 'phonebook') {
                    switch($roleId) {
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                        case 9: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            #endregion

            #region Языковые надбавки
            case 'langpremium':
            case 'teacherlangpremium':
                switch($roleId) {
                    case 3: $result = true; break;
                    default: $result = false;
                }
                break;
            #endregion

            #region Языковые Ставки
            case 'edunormteacher':
                if ($action === 'create') {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default:
                            if ((int)Yii::$app->session->get('user.uid') === 296) {
                                return true;
                            } else {
                                $result = false;
                            }
                    }
                } else {
                    switch($roleId) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            #endregion
		}
		return $result;
	}
}
