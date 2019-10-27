<?php

namespace app\controllers;

use app\models\Lang;
use app\models\User;
use app\models\Book;
use app\models\BookOrder;
use app\models\forms\BookForm;
use app\models\search\BookSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        /** @var BookOrder $bookOrder */
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
            'languages'         => Lang::getLanguagesSimple(),
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

        $model = new BookForm();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Учебник успешно добавлен.');
                return $this->redirect(['book/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить учебник.');
            }
        }

        return $this->render('create', [
            'model'         => $model,
            'languages'     => Lang::getLanguagesSimple(),
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

        $book = $this->findModel($id);
        if (empty($book)) {
            throw new NotFoundHttpException();
        }
        $model = new BookForm();
        $model->loadFromBook($book);
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Учебник успешно обновлен.');
                return $this->redirect(['book/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось обновить учебник.');
            }
        }

        return $this->render('update', [
            'model'         => $model,
            'languages'     => Lang::getLanguagesSimple(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
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
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
