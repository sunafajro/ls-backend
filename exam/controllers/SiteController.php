<?php

namespace exam\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use exam\models\forms\LoginForm;
use exam\models\LoginLog;

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
                'only' => ['index', 'login', 'logout', 'csrf'],
                'rules' => [
                    [
                        'actions' => ['login', 'csrf'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login', 'logout', 'index', 'csrf'],
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
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }

    /**
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($this->getDefaultAction());
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // TODO перенести в события
            $login               = new LoginLog();
            $login->result       = LoginLog::ACTION_LOGIN;
            $login->ipaddr       = Yii::$app->request->userIP;
            if (!$login->save()) {
                Yii::error("Не удалось сохранить информацию о входе пользователя #{$login->user_id} в систему.");
            }

            return $this->redirect($this->getDefaultAction());
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
     * @return array|string[]
     */
    private function getDefaultAction() : array
    {
        return ['site/index'];
    }
}
