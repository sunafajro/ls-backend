<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Moneystud;
use app\models\Notification;
use app\models\Student;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class NotificationController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
		    'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'resend'],
                'rules' => [
                    [
                        'actions' => ['create', 'resend'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create', 'resend'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['POST'],
                    'resend' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate(string $type, string $id)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
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

    public function actionResend(string $id)
    {
        if ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        $notification = Notification::findOne($id);
        if ($notification !== NULL) {
            if ($notification->status !== Notification::STATUS_QUEUE) {
                $notification->status = Notification::STATUS_QUEUE;
                if ($notification->save()) {
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
