<?php

namespace school\controllers;

use school\models\Auth;
use school\models\BookOrder;
use school\models\searches\BookOrderSearch;
use school\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * BookOrderController implements the CRUD actions for BookOrder model.
 */
class BookOrderController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['close', 'create', 'index', 'open', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => [
                            'close',
                            'create',
                            'index',
                            'open',
                            'update',
                            'delete',
                        ],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'close',
                            'create',
                            'index',
                            'open',
                            'update',
                            'delete',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'close'  => ['post'],
                    'delete' => ['post'],
                    'open'   => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BookOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $bookOrder = BookOrder::getCurrentOrder();
        if (!empty($bookOrder)) {
            $bookOrderCounters                  = $bookOrder->getOrderCounters();
            $bookOrderCounters['positions']     = $bookOrder->positions;
            $bookOrderCounters['positionCount'] = count($bookOrder->positions);
        }
        return $this->render('index', [
            'bookOrder'         => $bookOrder ?? null,
            'bookOrderCounters' => $bookOrderCounters ?? [],
            'dataProvider'      => $dataProvider,
            'searchModel'       => $searchModel,
            'userInfoBlock'     => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $model = new BookOrder();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый заказ успешно открыт.');
                return $this->redirect(['book/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось открыть новый заказ.');
            }
        }

        return $this->render('create', [
            'model'         => $model,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        return $this->render('update', [
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionOpen($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $this->findModel($id)->open();

        return $this->redirect(['book/index']);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionClose($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $this->findModel($id)->close();

        return $this->redirect(['book/index']);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionDelete($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return BookOrder
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = BookOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}