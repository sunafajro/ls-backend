<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\Breadcrumbs;
use app\models\ContactForm;
use app\models\Kaslibro;
use app\models\LoginForm;
use app\models\LoginLog;
use app\models\Navigation;
use app\models\Message;
use app\models\News;
use app\models\Ticket;
use app\models\Tool;
use app\models\User;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['csrf', 'bc', 'index', 'login', 'logout', 'nav', 'state'],
                'rules' => [
                    [
                        'actions' => ['csrf', 'index', 'login', 'state'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['bc', 'logout', 'nav'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['csrf', 'bc', 'index', 'logout', 'nav', 'state'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /* метод возвращает csrf токен для использования в post запросах */
    public function actionCsrf()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
        ];
    }

    /* метод возвращает массив со списком элементов хлебных крошек */
    public function actionBc()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Breadcrumbs::getItems();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {            
            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                $user = (new \yii\db\Query())
                ->select('u.id as uid, u.login as ulogin, u.name as uname, u.status as ustatus, st.name as stname, o.id as uoffice_id, o.name as uoffice, u.calc_teacher as uteacher, u.calc_city as ucity, u.logo as ulogo')
                ->from('user u')
                ->leftJoin('status st','st.id=u.status')
                ->leftJoin('calc_office o','o.id=u.calc_office')
                ->where('u.id=:id',[':id' => Yii::$app->user->identity->id])
                ->one();
    
                // заносим необходимые параметры пользователя в $_SESSION
                Yii::$app->session->set('user.uid',$user['uid']);
                Yii::$app->session->set('user.ulogin',$user['ulogin']);
                Yii::$app->session->set('user.uname',$user['uname']);
                Yii::$app->session->set('user.ustatus',$user['ustatus']);
                Yii::$app->session->set('user.stname',$user['stname']);
                Yii::$app->session->set('user.uoffice_id',$user['uoffice_id']);
                Yii::$app->session->set('user.uoffice',$user['uoffice']);
                Yii::$app->session->set('user.uteacher',$user['uteacher']);
                Yii::$app->session->set('user.ucity',$user['ucity']);
                Yii::$app->session->set('user.ulogo',$user['ulogo']);
                Yii::$app->session->set('user.sidebar', 2);
    
                // пишем лог входа в систему
                $login = new LoginLog();
                $login->date = date('Y-m-d H:i:s');
                $login->result = 1;
                $login->user_id = Yii::$app->session->get('user.uid');
                $login->ipaddr = Yii::$app->request->userIP;
                $login->save();
    
                return [
                    'id' => $user['uid'],
                    'loggedIn' => true,
                    'role' => [
                        'id' => $user['ustatus'],
                        'name' => $user['stname']
                    ],
                    'status' => true,
                    'text' => Yii::t('app', 'Login successfull!'),
                    'url' => User::getUserHomeUrl(),
                    'userName' => $user['uname']
                ];
            } else {
                return [
                    'status' => false,
                    'text' => Yii::t('app', 'Username or password not valid!')
                ];
            } 
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        }
    }

    public function actionLogout()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->post()) {
            $login = new LoginLog();
            $login->date = date('Y-m-d H:i:s');
            $login->result = 2;
            $login->user_id = Yii::$app->session->get('user.uid');
            $login->ipaddr = Yii::$app->request->userIP;
            $login->save();

            Yii::$app->user->logout();

            return [
                'id' => 0,
                'loggedIn' => false,
                'role' => [
                    'id' => 0,
                    'name' => 'guest'
                ],
                'status' => true,
                'text' => Yii::t('app', 'Logout successfull!'),
                'url' => User::getUserHomeUrl(),
                'userName' => 'guest',                
            ];
        } else {
            Yii::$app->response->statusCode = 405;
            return Tool::methodNotAllowed();
        } 
    }

    /* метод возвращает массив со списком элементов навигации */
    public function actionNav()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->post('type')) {
            switch (Yii::$app->request->post('type')) {
              /* запрашиваем только счетчики */
              case 'counters': return Navigation::getCounters(); break;
              /* запрашиваем из модели элементы навигации и возвращаем массив */
              default: return Navigation::getItems();
            }
        } else {
            /* запрашиваем из модели элементы навигации и возвращаем массив */
            return Navigation::getItems();
        }
    }

    public function actionState() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return [
                'id' => 0,
                'loggedIn' => false,
                'role' => [
                    'id' => 0,
                    'name' => 'guest'
                ],
                'status' => true,
                'url' => User::getUserHomeUrl(),
                'userName' => 'guest',                
            ];
        } else {
            return [
                'id' => Yii::$app->session->get('user.uid'),
                'loggedIn' => true,
                'role' => [
                    'id' => Yii::$app->session->get('user.ustatus'),
                    'name' => Yii::$app->session->get('user.stname'),
                ],
                'status' => true,
                'url' => User::getUserHomeUrl(),
                'userName' => Yii::$app->session->get('user.uname'),                
            ];
        }
    }

    protected static function getUrlParams(array $params)
    {
        if(!empty(Yii::$app->request->get())) {
            foreach(Yii::$app->request->get() as $key => $value) {
                if(array_key_exists($key, $params)) {
                    $params[$key] = $value;
                }
            }
        }
        return $params;
    }
}
