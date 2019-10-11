<?php

namespace app\controllers;

use app\models\Book;
use app\models\BookOrder;
use app\models\BookOrderPosition;
use app\models\Lang;
use app\models\Office;
use app\models\User;
use app\models\search\BookOrderPositionSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * BookOrderPositionController implements the CRUD actions for BookPositionOrder model.
 */
class BookOrderPositionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'create',
                            'update',
                            'delete',
                        ],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'index',
                            'create',
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        /** @var BookOrder $bookOrder */
        $bookOrder = BookOrder::find()->andWhere(['id' => $id])->one();
        $searchModel = new BookOrderPositionSearch();
        $dataProvider = $searchModel->search($bookOrder, Yii::$app->request->get());

        return $this->render('index', [
            'bookOrder'         => $bookOrder,
            'bookOrderCounters' => $bookOrder->getOrderCounters(),
            'dataProvider'      => $dataProvider,
            'languages'         => Lang::getLanguagesSimple(),
            'positions'         => $bookOrder->positions ?? [],
            'searchModel'       => $searchModel,
            'userInfoBlock'     => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate($id, $book_id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $bookOrder = BookOrder::find()->andWhere(['id' => $id])->one();
        $book = Book::find()->andWhere(['id' => $book_id])->one();

        if (empty($bookOrder) && empty($book)) {
            throw new NotFoundHttpException();
        }

        $model = new BookOrderPosition();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->book_id = $book_id;
            $model->book_order_id = $id;
            $model->purchase_cost_id = $book->purchaseCost->id ?? null;
            $model->selling_cost_id  = $book->sellingCost->id  ?? null;
            if ((int)Yii::$app->session->get('user.ustatus') === 4) {
                $model->office_id = (int)Yii::$app->session->get('user.uoffice_id');
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новая позиция в заказ успешно добавлена.');
                return $this->redirect(['book/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новую позицию в заказ.');
            }
        }

        return $this->render('create', [
            'book'          => $book,
            'bookOrder'     => $bookOrder,
            'model'         => $model,
            'offices'       => Office::getOfficesListSimple(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }
}