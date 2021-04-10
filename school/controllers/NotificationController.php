<?php

namespace school\controllers;

use Yii;
use school\models\Moneystud;
use school\models\Notification;
use school\models\Student;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class NotificationController
 * @package school\controllers
 */
class NotificationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $rules = ['create', 'resend'];
        return [
		    'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'resend' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param string $type
     * @param string $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate(string $type, string $id)
    {
        if ($type === Notification::TYPE_PAYMENT) {
            $student = (new \yii\db\Query())
            ->select(['email' => 's.email'])
            ->from(['p' => Moneystud::tableName()])
            ->innerJoin(['s' => Student::tableName()], 'p.calc_studname = s.id')
            ->where(['p.id' => $id])
            ->one();
            if (!($student['email'] ?? false)) {
                throw new NotFoundHttpException(Yii::t('app', 'Fail! Client not have an e-mail address!'));
            }
        }
        $notification = new Notification();
        $notification->entity_id = $id;
        $notification->type      = $type;
        $notification->user_id   = Yii::$app->session->get('user.uid');
        if ($notification->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'E-mail notification successfully added to queue!'));
        } else {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Failed to add e-mail notification to queue!'));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionResend(string $id)
    {
        $notification = Notification::findOne($id);
        if ($notification !== NULL) {
            if ($notification->status !== Notification::STATUS_QUEUE) {
                $notification->status = Notification::STATUS_QUEUE;
                if ($notification->save(true, ['status'])) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'E-mail notification successfully added to queue!'));
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Failed to add e-mail notification to queue!'));
                }
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app', 'E-mail notification successfully added to queue!'));
            }
        } else {
            throw new NotFoundHttpException(Yii::t('app','Object not found!'));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}
