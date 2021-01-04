<?php

namespace app\modules\exams\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\modules\exams\models\forms\LoginForm;
use app\modules\exams\models\LoginLog;

/**
 * Class SiteController
 * @package app\modules\exams\controllers
 */
class SiteController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors() : array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'login', 'logout', 'csrf'],
                'rules' => [
                    [
                        'actions' => ['index', 'login', 'csrf'],
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
    public function actions() : array
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
     * @return mixed
     */
    public function actionGetExams()
    {
        $exams = Yii::$app->cache->getOrSet('exams_data', function() {
            $exams = [];
            foreach (['ege', 'oge'] as $fileName) {
                $filePath = Yii::getAlias("@exams/{$fileName}.json");
                if (file_exists($filePath)) {
                    $rawExamsData = file_get_contents($filePath);
                    $jsonExamsData = json_decode($rawExamsData, true);
                    $jsonExamsData = array_filter($jsonExamsData, function ($exam) {
                        return $exam['enabled'];
                    });
                    $exams = array_merge($exams, $jsonExamsData);
                }
            }
            return $exams;
        }, 3600);

        return $this->asJson($exams);
    }

    /**
     * Для js приложений
     * @return mixed
     */
    public function actionCsrf()
    {
        return $this->asJson([
            Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
        ]);
    }

    /**
     * @return array|string[]
     */
    private function getDefaultAction() : array
    {
        return ['site/index'];
    }
}
