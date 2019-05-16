<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class MailPaymentReceiptsController extends Controller
{
    public function actionSend(string $email)
    {
        if ($email) {
            Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($email)
            ->setSubject('Message subject')
            ->setTextBody('Plain text content')
            ->setHtmlBody('<b>HTML content</b>')
            ->send();
            echo 'Email sended!';
        } else {
            echo 'Empty email!';
        }
    }
}
