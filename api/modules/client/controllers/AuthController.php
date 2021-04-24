<?php

namespace api\modules\client\controllers;

use api\modules\client\models\forms\LoginForm;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class AuthController
 * @package api\modules\client\controllers
 */
class AuthController extends Controller
{
    /**
     * @return array
     * @throws UnauthorizedHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin(): array
    {
        $form = new LoginForm();
        $form->load(\Yii::$app->request->post(), '');

        $auth = $form->validateAndGetAuth();

        if (empty($auth)) {
            throw new UnauthorizedHttpException('Неправильный логин или пароль.');
        }

        if (!$auth->resetAccessToken()) {
            throw new ServerErrorHttpException('Произошла ошибка.');
        }

        return [
            'id' => $auth->id,
            'name' => $auth->name,
            'accessToken' => $auth->accessToken,
        ];
    }
}