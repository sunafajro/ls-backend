<?php

namespace school\controllers;

use school\models\Auth;
use school\models\News;
use school\models\forms\LoginForm;
use school\models\LoginLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Class SiteController
 * @package school\controllers
 */
class SiteController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
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
    public function actions(): array
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
        $params = ['month' => date('m'), 'year' => date('Y')];
        $urlParams = self::getUrlParams($params);

        return $this->render('index', [
            'urlParams' => $urlParams,
            'news' => News::find()
                ->active()
                ->andFilterWhere(['MONTH(date)' => $urlParams['month']])
                ->andFilterWhere(['YEAR(date)' => $urlParams['year']])
                ->all(),
        ]);
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
            $login = new LoginLog();
            $login->result = LoginLog::ACTION_LOGIN;
            $login->ipaddr = Yii::$app->request->userIP;
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
     * @return mixed
     */
    public function actionCsrf()
    {
        return $this->asJson([
            Yii::$app->request->csrfParam => Yii::$app->request->getCsrfToken()
        ]);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private static function getUrlParams(array $params): array
    {
        if (!empty(Yii::$app->request->get())) {
            foreach (Yii::$app->request->get() as $key => $value) {
                if (array_key_exists($key, $params)) {
                    $params[$key] = $value;
                }
            }
        }
        return $params;
    }

    /**
     * @return string[]
     */
    private function getDefaultAction(): array
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        switch ($auth->roleId) {
            case 3:
                return ['report/common'];
            case 4:
                return ['call/index'];
            case 5:
                return ['teacher/view', 'id' => $auth->teacherId];
            case 6:
                return ['teacher/index'];
            case 9:
                return ['translate/translations'];
            case 11:
                return ['payment/create'];
            default:
                return ['site/index'];
        }
    }
}
