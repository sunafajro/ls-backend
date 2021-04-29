<?php

namespace client\controllers;

use client\models\Auth;
use client\models\LoginLog;
use common\models\BasePollResponse as PollResponse;
use common\models\BasePollQuestionResponse as QuestionResponse;
use Yii;
use client\models\forms\LoginForm;
use client\models\Student;
use common\models\BasePoll as Poll;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ConflictHttpException;
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
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        $student = Student::findOne($auth->id);
        $messages = $student->getNews();
        list($comments) = $student ? $student->getLessonsComments(5, 0) : [[], []];
        $pollModel = null;
        foreach (Poll::find()->byEntityType(Poll::ENTITY_TYPE_CLIENT)->inProgress()->active()->all() as $poll) {
            if (!$poll->getResponses()->andWhere(['user_id' => $auth->id])->exists()) {
                $pollModel = $poll;
                break;
            }
        }
        return $this->render('index', [
            'poll'     => $pollModel,
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
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        $pollResponseData = \Yii::$app->request->post('PollResponse', null);
        if (empty($pollResponseData)) {
            throw new BadRequestHttpException('Необходимо ответить хотя бы на один вопрос.');
        }

        $poll = Poll::find()->active()->inProgress()->byid($id)->one();
        if ($poll->getResponses()->andWhere(['user_id' => $auth->id])->exists()) {
            throw new ConflictHttpException('Вы уже отвечали на данный опрос.');
        }

        $t = \Yii::$app->db->beginTransaction();
        try {
            $pollResponse = new PollResponse([
                'poll_id' => $poll->id,
                'user_id' => $auth->id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            if (!$pollResponse->save()) {
                throw new \Exception('Ошибка сохранения ответа на опрос.');
            }
            foreach ($poll->questions as $question) {
                $questionResponse = new QuestionResponse([
                    'poll_id' => $poll->id,
                    'poll_question_id' => $question->id,
                    'poll_response_id' => $pollResponse->id,
                    'user_id' => $auth->id,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $questionResponseItems = [];
                foreach ($question->items as $item) {
                    if (!isset($questionResponseItems[$item['id']])) {
                        $questionResponseItems[$item['id']] = [];
                    }
                    $questionResponseItemData = $pollResponseData[$question->id][$item['id']] ?? null;
                    $questionResponseItems[$item['id']]['value'] = !empty($questionResponseItemData['value']) ? (int)$questionResponseItemData['value'] : 0;
                    if (!empty($item['textInput'])) {
                        $questionResponseItems[$item['id']]['text'] = !empty($questionResponseItemData['text']) ? $questionResponseItemData['text'] : '';
                    }
                    foreach ($item['options'] ?? [] as $option) {
                        if (!isset($questionResponseItems[$item['id']]['options'])) {
                            $questionResponseItems[$item['id']]['options'] = [];
                        }
                        if (!isset($questionResponseItems[$item['id']]['options'][$option['id']])) {
                            $questionResponseItems[$item['id']]['options'][$option['id']] = [];
                        }
                        $questionResponseItemOptionData = $questionResponseItemData['options'][$option['id']] ?? null;
                        $questionResponseItems[$item['id']]['options'][$option['id']]['value'] = !empty($questionResponseItemOptionData['value']) ? (int)$questionResponseItemOptionData['value'] : 0;
                        if (!empty($option['textInput'])) {
                            $questionResponseItems[$item['id']]['options'][$option['id']]['text'] = !empty($questionResponseItemOptionData['text']) ? $questionResponseItemOptionData['text'] : '';
                        }
                    }
                }
                $questionResponse->items = $questionResponseItems;
                if (!$questionResponse->save()) {
                    throw new \Exception('Ошибка сохранения ответа на вопрос.');
                }
            }
            $t->commit();
            \Yii::$app->session->setFlash('success', 'Ответы на опрос успешно приняты.');
        } catch (\Exception $e) {
            $t->rollBack();
            \Yii::$app->session->setFlash('error', 'Не удалось сохранить ответы на опрос.');
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
