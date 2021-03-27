<?php

namespace school\controllers;

use school\models\Auth;
use school\models\Lang;
use school\models\Book;
use school\models\BookOrder;
use school\models\forms\BookForm;
use school\models\searches\BookSearch;
use school\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class BookController
 * @package school\controllers
 */
class BookController extends Controller
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = ['index', 'create', 'update', 'delete', 'office-index'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $rules,
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
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
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
        ]);
    }

    /**
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
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
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        $this->layout = 'main-2-column';
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 7])) {
            throw new ForbiddenHttpException( 'Вам не разрешено производить данное действие.');
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
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
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
     * @return Book
     * @throws NotFoundHttpException
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
