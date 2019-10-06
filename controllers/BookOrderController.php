<?php

namespace app\controllers;

use app\models\BookOrder;
use app\models\search\BookOrderSearch;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BookOrderController implements the CRUD actions for BookOrder model.
 */
class BookOrderController extends Controller
{
    public function behaviors()
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

        return $this->render('index', [
            'bookOrder'         => $bookOrder ?? null,
            'bookOrderCounters' => $bookOrder ? $bookOrder->getOrderCounters() : [],
            'dataProvider'      => $dataProvider,
            'searchModel'       => $searchModel,
            'userInfoBlock'     => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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
     */
    public function actionUpdate($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        return $this->render('update', [
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionOpen($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $this->findModel($id)->open();

        return $this->redirect(['book/index']);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionClose($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $this->findModel($id)->close();

        return $this->redirect(['book/index']);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BookOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BookOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
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