<?php

namespace api\modules\client\controllers;

use api\modules\client\models\News;
use yii\data\ActiveDataProvider;
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
     * {@inheritdoc}
     */
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

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
                        'verbs' => ['GET'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $requestParams = \Yii::$app->getRequest()->getQueryParams();
        $query = News::find();

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => (new News())->fields(),
                'params' => $requestParams,
            ],
        ]);
    }
}