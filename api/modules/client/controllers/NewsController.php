<?php

namespace api\modules\client\controllers;

use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

/**
 * Class NewsController
 * @package api\modules\client\controllers
 */
class NewsController extends Controller
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'authenticator' => HttpBearerAuth::class,
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'verbs' => ['POST'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return [];
    }
}