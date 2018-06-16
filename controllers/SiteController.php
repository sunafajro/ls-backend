<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
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
                'only' => ['login', 'logout', 'index', 'reference', 'sidebar', 'nav', 'csrf'],
                'rules' => [
                    [
                        'actions' => ['login', 'csrf'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'reference', 'nav'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index', 'reference', 'sidebar', 'nav', 'csrf'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'nav' => ['post']
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

    public function actionIndex()
    {
        $userInfoBlock = User::getUserInfoBlock();

        $params = ['month' => date('m'), 'year' => date('Y')];
        $url_params = self::getUrlParams($params);

        return $this->render('index',[
            'url_params' => $url_params,
			'news' => News::getNewsList($url_params['month'], $url_params['year']),
            'months' => Tool::getMonthsSimple(),
            'userInfoBlock' => $userInfoBlock
	    ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        // если логин прошел успешно
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // запрашиваем по юзеру данные из базы
            $user = (new \yii\db\Query())
            ->select('u.id as uid, u.login as ulogin, u.name as uname, u.status as ustatus, st.name as stname, o.id as uoffice_id, o.name as uoffice, u.calc_teacher as uteacher, u.calc_city as ucity, u.logo as ulogo')
            ->from('user u')
            ->leftJoin('status st','st.id=u.status')
            ->leftJoin('calc_office o','o.id=u.calc_office')
            ->where('u.id=:id',[':id'=>Yii::$app->user->identity->id])
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

            // переподим пользователя на нужную страничку
            switch(Yii::$app->session->get('user.ustatus')){
                case 3: return $this->redirect(['report/index', 'type'=>1]); break;
                case 4: return $this->redirect(['call/index']); break;
                case 5: return $this->redirect(['teacher/view','id'=>Yii::$app->session->get('user.uteacher')]); break;
				case 6: return $this->redirect(['teacher/index']); break;
                default: return $this->redirect(['site/index']);
            }            
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        $login = new LoginLog();
        $login->date = date('Y-m-d H:i:s');
        $login->result = 2;
        $login->user_id = Yii::$app->session->get('user.uid');
        $login->ipaddr = Yii::$app->request->userIP;
        $login->save();

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSidebar()
    {
        switch(Yii::$app->session->get('user.sidebar')) {
            case 1: Yii::$app->session->set('user.sidebar', 2); break;
            case 2: Yii::$app->session->set('user.sidebar', 1); break;
            default: Yii::$app->session->set('user.sidebar', 1); break;
        }
        return $this->redirect(Yii::$app->request->referrer);
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

    public function actionCsrf()
    {
        /* включаем формат ответа JSON */
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
        ];
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
