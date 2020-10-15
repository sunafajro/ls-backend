<?php

namespace app\modules\school\models;

use app\models\BaseUser;
use app\models\City;
use app\models\Office;
use app\models\Role;
use app\models\Teacher;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Html;

/**
 * This is the model class for table "user".
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
    public static function tableName() : string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['site'], 'default', 'value' => 0],
            [['site', 'visible', 'status', 'calc_office', 'calc_teacher', 'calc_city'], 'integer'],
            [['login', 'pass', 'name', 'logo'], 'string'],
            [['login'], 'unique', 'on' => 'create',
                'when' => function ($model) {
                    return static::findUserByUsername($model->login) !== NULL;
                }],
            [['login'], 'unique', 'on' => 'update', 
                'when' => function ($model) {
                    return static::getPrevUsername($model->id) !== $model->login;
                }],
            [['login', 'name'], 'string', 'min' => 3],
            [['pass'], 'string', 'min' => 8],
            [['login', 'pass', 'name', 'status'], 'required'],
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
     * @param array $condition
     *
     * @return array
     */
    public static function findUserByCondition(array $condition)
    {
        return self::find()
            ->select([
                'id'         => 'user.id',
                'username'   => 'user.login',
                'password'   => 'user.pass',
                'fullName'   => 'user.name',
                'roleId'     => 'roles.id',
                'roleName'   => 'roles.name',
                'teacherId'  => 'user.calc_teacher',
                'officeId'   => 'offices.id',
                'officeName' => 'offices.name',
                'cityId'     => 'cities.id',
                'cityName'   => 'cities.name',
            ])
            ->innerJoin(['roles'  => Role::tableName()], "roles.id = user.status")
            ->leftJoin(['offices' => Office::tableName()], "offices.id = user.calc_office")
            ->leftJoin(['cities'  => City::tableName()], "cities.id = offices.calc_city")
            ->where([
                'user.visible' => 1,
            ])
            ->andWhere($condition)
            ->asArray()
            ->one();
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public static function findUserById(int $id) : array
    {
        return self::findUserByCondition([
            'user.id' => $id,
        ]);
    }

    /**
     * @param $username
     *
     * @return array
     */
    public static function findUserByUsername(string $username) : array
    {
        return self::findUserByCondition([
            'user.login' => trim($username),
        ]);
    }

    /**
     * @param int $id
     * @return string|null
     */
    public static function getPrevUsername(int $id)
    {
        return self::findUserById($id)['username'] ?? null;
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
     * @return mixed
     */
    public static function getUserListFiltered($params)
    {
        return (new Query())
	        ->select([
                'id' => 'u.id',
                'name' => 'u.name',
                'login' => 'u.login',
                'roleId' => 'u.status',
                'role' => 's.name',
                'office' => 'o.name',
                'visible' => 'u.visible'
            ])
	    ->from(['u' => 'user'])
        ->leftJoin('status s','u.status=s.id')
        ->leftJoin('calc_office o', 'u.calc_office=o.id')
	    ->andFilterWhere(['u.visible'=> $params['active']])
        ->andFilterWhere(['u.status'=> $params['role']])
	    ->orderBy(['s.id'=>SORT_ASC,'u.name'=>SORT_ASC])
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
            ->select('u.id as uid, u.name as uname, u.login as ulogin, u.visible as vis, s.name as urole, o.name as uoffice, u.logo as ulogo')
            ->from('user u')
            ->leftjoin('status s', 's.id=u.status')
            ->leftjoin('calc_office o', 'o.id=u.calc_office')
            ->where('u.id=:id', [':id'=>$id])
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
