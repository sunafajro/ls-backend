<?php

namespace client\controllers;

use client\models\LoginLog;
use Yii;
use client\models\forms\LoginForm;
use client\models\Student;
use common\models\BasePoll as Poll;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Class SiteController
 * @package client\controllers
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'login', 'logout', 'save-poll-answers'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'logout', 'save-poll-answers'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'save-poll-answers' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
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
        $student = Student::findOne(Yii::$app->user->id);
        $messages = $student->getNews();
        list($comments) = $student ? $student->getLessonsComments(5, 0) : [[], []];
        return $this->render('index', [
            'poll'    => Poll::find()->byEntityType(Poll::ENTITY_TYPE_CLIENT)->inProgress()->active()->one(),
            'messages' => $messages,
            'comments' => $comments,
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
        $login->result = \exam\models\LoginLog::ACTION_LOGOUT;
        $login->ipaddr = Yii::$app->request->userIP;
        if (!$login->save()) {
            Yii::error("Не удалось сохранить информацию о выходе пользователя #{$login->user_id} из системы.");
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSavePollAnswers($id)
    {
        $pollResponseData = \Yii::$app->request->post('PollResponse', null);
        if (empty($pollResponseData)) {
            throw new BadRequestHttpException('Необходимо ответить хотя бы на один вопрос.');
        }


        return $this->redirect(\Yii::$app->request->referrer);
    }

    /**
     * @return array|string[]
     */
    private function getDefaultAction() : array
    {
        return ['site/index'];
    }
}
