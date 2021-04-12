<?php

namespace console\controllers;

use Yii;
use school\models\Notification;
use yii\console\Controller;

/**
 * Class SendEmailController
 * @package console\controllers
 */
class SendEmailController extends Controller
{
    public function actionSend()
    {
        $notification = new Notification();
        $notifications = $notification->getNotificationsByStatus(Notification::STATUS_QUEUE);
        foreach($notifications ?? [] as $n) {
            $subject = Notification::getNotificationSubject($n['notificationType']);
            $body = Notification::getNotificationBody($n['notificationType'], $n['recipientName'], $n['paymentDate'], $n['paymentValue']);

            $notify = Notification::findOne($n['notificationId']);
            try {
                $mailer = Yii::$app->mailer->compose()
                    ->setFrom(Yii::$app->params['notificationEmail'])
                    ->setTo([trim($n['recipientEmail']) => $n['recipientName']])
                    ->setSubject($subject)
                    ->setHtmlBody($body);

                $notify->count += 1;
                if ($mailer->send()) {
                    $notify->status = Notification::STATUS_SUCCESS;
                    $notify->save(true, ['status']);
                } else {
                    throw new \Exception('Не удалось отправить сообщение.');
                }
            } catch (\Exception $e) {
                $notify->status = Notification::STATUS_FAIL;
                $notify->save(true, ['status']);
                Yii::error($e->getMessage());
            }
        }
    }
}
