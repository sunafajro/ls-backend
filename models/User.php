<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $site
 * @property integer $visible
 * @property string $login
 * @property string $pass
 * @property string $name
 * @property integer $status
 * @property integer $calc_office
 * @property integer $calc_teacher
 * @property integer $calc_city
 * @property string $logo
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    //public $id;
    public $username;
    public $password;
    public $pass_repeat;
    //public $authKey;
    //public $accessToken;

    private static $user;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site', 'visible', 'status', 'calc_office', 'calc_teacher', 'calc_city'], 'integer'],
            [['login', 'pass', 'name', 'status'], 'required'],
            [['login', 'pass', 'name', 'logo'], 'string'],
            [['login'], 'unique', 'on' => 'create',
                'when' => function ($model) {
                    return static::findUserByUsername($model->login) !== NULL;
                }],
            [['login'], 'unique', 'on' => 'update', 
                'when' => function ($model) {
                    return static::getPrevUsername($model->id) !== $model->login;
                }],
            [['login'], 'string', 'min' => 3],
            [['name'], 'string', 'min' => 3],
            [['pass'], 'string', 'min' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site' => 'Site',
            'visible' => 'Visible',
            'login' => \Yii::t('app','User login'),
            'pass' => \Yii::t('app','Password'),
            'name' => \Yii::t('app','Full name'),
            'status' => \Yii::t('app','User status'),
            'calc_office' => \Yii::t('app','Office'),
            'calc_teacher' => \Yii::t('app','Teacher'),
            'calc_city' => \Yii::t('app','City'),
            'logo' => \Yii::t('app','User logo'),
        ];
    }

    public static function findUserById($id)
    {
       return (new \yii\db\Query())
           ->select('id as id, login as username, pass as password')
           ->from('user')
           ->where('id=:id AND visible=:one', 
           [':id' => $id, ':one' => 1])
           ->one();
    }

    public static function findUserByUsername($username)
    {
        return (new \yii\db\Query())
            ->select('id as id, login as username, pass as password')
            ->from('user')
            ->where('login=:login AND visible=:one', 
            [':login' => trim($username), ':one' => 1])
            ->one();
    }

    public static function getPrevUsername($id)
    {
        return static::findUserById($id)['username'];
    }

    public static function findByUsername($username)
    {
        $users = self::find()->where('login=:uname' and 'visible=:vis', [':uname'=>trim($username), ':vis'=>'1'])->all();
        
        if($users !== null) {
            foreach($users as $user){
            if (strcasecmp($user['login'], trim($username)) === 0) {
                return new static($user);
            }}}
            
        
        return null;
    }
    
    public function validatePassword($password)
    {
        return $this->pass === md5(trim($password));
    }

    public static function findIdentity($id)
    {
       return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Метод генерирует html блок с краткой информацией о текущем пользователе.
     * Блок размещается в боковое меню.
     * @return mixed
     */
    public static function getUserInfoBlock()
    {
        $str = '';
        $str .= '<div class="well well-sm small">';
		$str .= '<span class="font-weight-bold">' . Yii::$app->session->get('user.uname') . '</span>';
        if(Yii::$app->session->get('user.uteacher')) {
            $str .= Html::a('', ['teacher/view', 'id' => Yii::$app->session->get('user.uteacher')], ['class'=>'fa fa-user btn btn-default btn-xs']);                   
        }            
        $str .= '<br />';
        $str .= Yii::$app->session->get('user.stname');
        if(Yii::$app->session->get('user.ustatus')==4) {
            $str .= '<br />';
            $str .= Yii::$app->session->get('user.uoffice');
        }
        $str .= '</div>';
        
        return $str;
    }

    /* возвращает данные пользователя, для информационного блока */
    public static function getUserInfo()
    {
        $userData = [];

        $userData['name'] = Yii::$app->session->get('user.uname');
        if(Yii::$app->session->get('user.uteacher')) {
            $userData['teacherId'] = Yii::$app->session->get('user.uteacher');
        } else {
            $userData['teacherId'] = null;
        }
        $userData['roleId'] = Yii::$app->session->get('user.ustatus');
        $userData['role'] = Yii::$app->session->get('user.stname');
        if((int)Yii::$app->session->get('user.ustatus') === 4) {
            $userData['office'] = Yii::$app->session->get('user.uoffice');
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
        $userlist = (new \yii\db\Query())
	->select(
          [
            'id' => 'u.id',
            'name' => 'u.name',
            'login' => 'u.login',
            'roleId' => 'u.status',
            'role' => 's.name',
            'office' => 'o.name',
            'visible' => 'u.visible'
          ]
        )
	->from(['u' => 'user'])
        ->leftJoin('status s','u.status=s.id')
        ->leftJoin('calc_office o', 'u.calc_office=o.id')
	->andFilterWhere(['u.visible'=> $params['active']])
        ->andFilterWhere(['u.status'=> $params['role']])
	->orderBy(['s.id'=>SORT_ASC,'u.name'=>SORT_ASC])
	->all();
        return $userlist;
    }

    /**
     * Возвращает информацию по одному пользователю.
     * @param integer $id
     * @return mixed
     */
    public static function getUserInfoById($id)
    {
		$user = (new \yii\db\Query())
		->select('u.id as uid, u.name as uname, u.login as ulogin, u.visible as vis, s.name as urole, o.name as uoffice, u.logo as ulogo')
		->from('user u')
		->leftjoin('status s', 's.id=u.status')
		->leftjoin('calc_office o', 'o.id=u.calc_office')
		->where('u.id=:id', [':id'=>$id])
        ->one();

        return $user;
    }

    /* Метод проверки ролей пользователей на доступ к ресурсам */
    public static function checkAccess($controller, $action) 
    {
        $result = true;
        switch($controller) {
            /* раздел Клиенты */
            case 'studname':
                if ($action === 'view' || $action === 'index') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        case 5: $result = true; break;
                        case 6: $result = true; break;
                        default: $result = false;
                    }
                } else if ($action === 'update' || $action === 'active' || $action === 'inactive' || $action === 'detail' || $action === 'change-office' || $action === 'update-debt') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else if ($action === 'merge' || $action === 'delete') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    $result = false;
                }
                return $result;
            /* подраздел счета клиента */
            case 'invoice':
                if (
                    $action === 'index' ||
                    $action === 'create' ||
                    $action === 'enable' ||
                    $action === 'disable' ||
                    $action === 'done' ||
                    $action === 'undone' ||
                    $action === 'remain' ||
                    $action === 'unremain' ||
                    $action === 'get-data' ||
                    $action === 'corp') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else if ($action === 'delete') {
                    switch(Yii::$app->session->get('user.ustatus')) {
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
                if ($action === 'approve' || $action === 'disableall') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else if($action === 'create' || $action === 'disable' || $action === 'enable') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
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
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        case 5: $result = true; break;
                        default: $result = false;
                    }
                }
                if ($action === 'create' || $action === 'delete' || $action = 'download-attestation') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            /* подраздел аттестации клиента */

            /* раздел Клиенты */
            /* раздел Переводы */
            case 'langtranslator':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    case 9: $result = true; break;
                    default:$result = false;
                }
                break;
            case 'translate':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    case 9: $result = true; break;
                    default: $result = false;
                }
                break;
            case 'translation':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    case 9: $result = true; break;
                    default: $result = false;		
				}
				break;
			case 'translationclient':
				switch(Yii::$app->session->get('user.ustatus')) {
					case 3: $result = true; break;
                    case 9: $result = true; break;
                    default: $result = false;			
				}
				break;
            case 'translationlang':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    case 9: $result = true; break;
                    default: $result = false;            
                }
                break;
            case 'translationnorm':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    case 9: $result = true; break;
                    default: $result = false;            
                }
                break;
            case 'translator':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    case 9: $result = true; break;
                    default: $result = false;        
                }
                break;
            /* раздел Переводы */

            /* раздел Скидки*/
            case 'sale':
                if ($action === 'create' || $action === 'update' || $action === 'delete') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                } else if($action === 'index' || $action === 'getsales') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    $result = false;
                }
                break;
            /* раздел Скидки*/

            /* раздел Справочники */
            case 'reference':
                if ($action === 'phonebook') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        case 4: $result = true; break;
                        case 5: $result = true; break;
                        case 6: $result = true; break;
                        case 7: $result = true; break;
                        case 8: $result = true; break;
                        case 9: $result = true; break;
                        default: $result = false;
                    }
                } else {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            /* подраздел Языковые надбавки */
            case 'langpremium':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    default: $result = false;
                }
                break;
            /* подраздел Языковые надбавки */
            /* раздел Справочники */

            /* раздел Преподаватели */
            /* подраздел Ставки */
            case 'edunormteacher':
                if ($action === 'create') {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        default:
                            if ((int)Yii::$app->session->get('user.uid') === 296) {
                                return true;
                            } else {
                                $result = false;
                            }
                    }
                } else {
                    switch(Yii::$app->session->get('user.ustatus')) {
                        case 3: $result = true; break;
                        default: $result = false;
                    }
                }
                break;
            /* подраздел Ставки */
            /* подраздел Языковые надбавки */
            case 'teacherlangpremium':
                switch(Yii::$app->session->get('user.ustatus')) {
                    case 3: $result = true; break;
                    default: $result = false;            
                }
                break;
            /* подраздел Языковые надбавки */
            /* раздел Преподаватели */
		}
		return $result;
	}
}
