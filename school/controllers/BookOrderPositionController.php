<?php

namespace school\controllers;

use school\models\Auth;
use school\models\Book;
use school\models\BookCost;
use school\models\BookOrder;
use school\models\BookOrderPosition;
use school\models\BookOrderPositionItem;
use school\models\Lang;
use school\models\Office;
use school\models\Student;
use school\models\User;
use school\models\searches\BookOrderPositionSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BookOrderPositionController implements the CRUD actions for BookPositionOrder model.
 */
class BookOrderPositionController extends Controller
{
    public function behaviors(): array
    {
        $rules = ['index', 'autocomplete', 'create', 'update', 'delete', 'change-items'];
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
                    'autocomplete' => ['post'],
                    'change-items' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        /** @var BookOrder $bookOrder */
        $bookOrder    = BookOrder::find()->andWhere(['id' => $id])->one();
        $searchModel  = new BookOrderPositionSearch();
        $dataProvider = $searchModel->search($bookOrder, Yii::$app->request->get());
        $officeId     = Yii::$app->request->get('BookOrderPositionSearch', [])['office'] ?? null;
        if (!empty($bookOrder)) {
            $bookOrderCounters = $bookOrder->getOrderCounters($officeId ?: null);
            $bookOrderCounters['positionCount'] = $bookOrder->getPositions()->andFilterWhere(['office_id' => $officeId])->count();
        }

        return $this->render('index', [
            'bookOrder'         => $bookOrder,
            'bookOrderCounters' => $bookOrderCounters ?? [],
            'dataProvider'      => $dataProvider,
            'languages'         => Lang::getLanguagesSimple(),
            'offices'           => Office::getOfficesListSimple(),
            'searchModel'       => $searchModel,
            'userInfoBlock'     => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param int $order_id идентификатор заказа
     * @param int $book_id идентификатор книги
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate($order_id, $book_id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $bookOrder = BookOrder::find()->andWhere(['id' => $order_id])->one();
        $book = Book::find()->andWhere(['id' => $book_id])->one();

        if (empty($bookOrder) && empty($book)) {
            throw new NotFoundHttpException('Не удалось найти учебник или заказ.');
        }

        $model = new BookOrderPosition();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->book_id = $book_id;
            $model->book_order_id = $order_id;
            $model->purchase_cost_id = $book->purchaseCost->id ?? null;
            $model->selling_cost_id  = $book->sellingCost->id  ?? null;
            if ($auth->roleId === 4) {
                $model->office_id = $auth->officeId;
            }
            /* предотвращает создание нескольких позиций по одному учебнику */
            $position = BookOrderPosition::find()->andWhere([
                'book_id'       => $model->book_id,
                'book_order_id' => $model->book_order_id,
                'office_id'     => $model->office_id,
                'visible'       => 1,
            ])->one();
            if (!empty($position)) {
                $position->count += $model->count;
                $position->paid  += $model->paid;
                $position->purchase_cost_id = $model->purchase_cost_id;
                $position->selling_cost_id  = $model->selling_cost_id;
                $model = $position;
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

    /**
     * @param int $id идентификатор позиции
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $model = $this->findModel($id);
        if (empty($model)) {
            throw new NotFoundHttpException('Позиция заказа не найдена');
        }
        if ($auth->roleId === 4 && $model->office_id !== $auth->officeId) {
            throw new ForbiddenHttpException('Доступ ограничен');
        }
        $bookOrder = BookOrder::find()->andWhere(['id' => $model->book_order_id])->one();
        $book = Book::find()->andWhere(['id' => $model->book_id])->one();

        if (empty($bookOrder) && empty($book)) {
            throw new NotFoundHttpException();
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Позиция заказа успешно обновлена.');
                return $this->redirect(['book/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось обновить позицию заказа.');
            }
        }

        return $this->render('update', [
            'book'          => $book,
            'bookOrder'     => $bookOrder,
            'itemModel'     => new BookOrderPositionItem(),
            'model'         => $model,
            'offices'       => Office::getOfficesListSimple(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $model = $this->findModel($id);
        if (empty($model)) {
            throw new NotFoundHttpException('Позиция заказа не найдена');
        }
        if ($auth->roleId === 4 && $model->office_id !== $auth->officeId) {
            throw new ForbiddenHttpException('Доступ ограничен');
        }
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Позиция заказа успешно удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить позицию заказа.');
        }
        return $this->redirect(['book/index']);
    }

    /**
     * @param int $id
     * @param string $action
     * @param int $itemId
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionChangeItems(int $id, string $action, int $itemId = null)
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $model = $this->findModel($id);
        if (empty($model)) {
            throw new NotFoundHttpException('Позиция заказа не найдена');
        }
        if ($auth->roleId === 4 && $model->office_id !== $auth->officeId) {
            throw new ForbiddenHttpException('Доступ ограничен');
        }
        $itemModel = new BookOrderPositionItem([
            'book_order_position_id' => $id,
        ]);
        if (!empty($itemId) && in_array($action, ['update', 'delete'])) {
            $itemModel = BookOrderPositionItem::find()->andWhere(['id' => $itemId, 'book_order_position_id' => $id])->one();
        }
        if (in_array($action, ['create', 'update'])) {
            $formName = $itemModel->formName();
            /** @var BookCost $sellingCost */
            $sellingCost = $model->getSellingCost()->one();
            $postData = Yii::$app->request->post();
            $postData[$formName]['paid'] = $sellingCost->cost * ($postData[$formName]['count'] ?? 0);
            if (isset($postData[$formName]['student_id']) && $postData[$formName]['student_id']) {
                $student = Student::find()->andWhere(['id' => $postData[$formName]['student_id']])->one();
                if (!empty($student)) {
                    $postData[$formName]['student_name'] = $student->name;
                } else {
                    unset($postData[$formName]['student_id']);
                }
            }
            if ($itemModel->load($postData) && $itemModel->save()) {
                Yii::$app->session->setFlash('success', $action === 'create' ? 'Запись успешно добавлена к позиции заказа.' : 'Запись успешно изменена.');
            } else {
                Yii::$app->session->setFlash('error', $action === 'create' ? 'Не удалось добавить запись к позиции заказа.' : 'Не удалось изменить запись');
            }
        } else if ($action === 'delete') {
            if (empty($itemModel)) {
                throw new NotFoundHttpException('Позиция заказа не найдена');
            }
            if ($itemModel->delete()) {
                Yii::$app->session->setFlash('success', 'Запись успешно удалена из позиции заказа.');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить запись из позиции заказа.');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $sid
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAutocomplete()
    {
        $students = Student::getStudentsAutocomplete(Yii::$app->request->post('term') ?? NULL);
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $students;
    }

    /**
     * @param integer $id
     * @return BookOrderPosition
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = BookOrderPosition::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}