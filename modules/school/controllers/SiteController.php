<?php

namespace app\modules\school\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use app\modules\school\models\LoginForm;
use app\modules\school\models\LoginLog;
use app\models\News;
use app\models\Tool;
use app\modules\school\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'logout', 'index', 'csrf'],
                'rules' => [
                    [
                        'actions' => ['login', 'csrf'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'index', 'csrf'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $params = ['month' => date('m'), 'year' => date('Y')];
        $url_params = self::getUrlParams($params);

        return $this->render('index',[
            'url_params'    => $url_params,
			'news'          => News::getNewsList($url_params['month'], $url_params['year']),
            'months'        => Tool::getMonthsSimple(),
            'userInfoBlock' => User::getUserInfoBlock()
	    ]);
    }

    /**
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // TODO удалить после того как в коде не останется использования данных сессии
            // @deprecated
            Yii::$app->session->set('user.uid',        Yii::$app->user->identity->id);
            Yii::$app->session->set('user.ulogin',     Yii::$app->user->identity->username);
            Yii::$app->session->set('user.uname',      Yii::$app->user->identity->fullName);
            Yii::$app->session->set('user.ustatus',    Yii::$app->user->identity->roleId);
            Yii::$app->session->set('user.stname',     Yii::$app->user->identity->roleName);
            Yii::$app->session->set('user.uoffice_id', Yii::$app->user->identity->officeId);
            Yii::$app->session->set('user.uoffice',    Yii::$app->user->identity->officeName);
            Yii::$app->session->set('user.uteacher',   Yii::$app->user->identity->teacherId);
            Yii::$app->session->set('user.ucity',      Yii::$app->user->identity->cityId);

            // TODO перенести в события
            $login               = new LoginLog();
            $login->result       = LoginLog::ACTION_LOGIN;
            $login->ipaddr       = Yii::$app->request->userIP;
            if (!$login->save()) {
                Yii::error("Не удалось сохранить информацию о входе пользователя #{$login->user_id} в систему.");
            }

            switch (Yii::$app->user->identity->roleId) {
                case 3:
                    return $this->redirect(['report/common']);
                case 4:
                    return $this->redirect(['call/index']);
                case 5:
                    return $this->redirect(['teacher/view', 'id' => Yii::$app->user->identity->teacherId]);
                case 6:
                    return $this->redirect(['teacher/index']);
                case 9:
                    return $this->redirect(['translate/translations']);
                case 11:
                    return $this->redirect(['moneystud/create']);
                default:
                    return $this->redirect(['site/index']);
            }            
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionLogout()
    {
        // TODO перенести в события
        $login = new LoginLog();
        $login->result = LoginLog::ACTION_LOGOUT;
        $login->ipaddr = Yii::$app->request->userIP;
        if (!$login->save()) {
            Yii::error("Не удалось сохранить информацию о выходе пользователя #{$login->user_id} из системы.");
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Для js приложений
     * @return array
     */
    public function actionCsrf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
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
