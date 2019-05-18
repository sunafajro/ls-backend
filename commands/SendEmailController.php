<?php

namespace app\commands;

use Yii;
use app\models\Notification;
use yii\console\Controller;

class SendEmailController extends Controller
{
    public function actionSend()
    {
        $notification = new Notification();
        $notifications = $notification->getNotificationsByStatus(Notification::STATUS_QUEUE);
        foreach($notifications ?? [] as $n) {
            $subject = Notification::getNotificationSubject($n['notificationType']);
            $body = Notification::getNotificationBody($n['notificationType'], $n['recipientName'], $n['paymentDate'], $n['paymentValue']);
            $mailer = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['notificationEmail'])
            ->setTo([$n['recipientEmail'] => $n['recipientName']])
            ->setSubject($subject)
            ->setHtmlBody($body);
            $notify = Notification::findOne($n['notificationId']);
            $notify->count += 1;
            if ($mailer->send()) {
                $notify->status = Notification::STATUS_SUCCESS;
                $notify->save();
            } else {
                $notify->status = Notification::STATUS_FAIL;
                $notify->save(); 
            }
        }
    }
}
