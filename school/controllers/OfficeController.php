<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Office;
use yii\web\Response;
use yii\filters\AccessControl;

/**
 * OfficeController implements the CRUD actions for Office model.
 */
class OfficeController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $actions = ['index'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => $actions,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $actions,
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status'  => true,
            'offices' => Office::getOfficesList(),
        ];
    }
}
