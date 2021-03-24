<?php

namespace school\controllers;

use school\models\Auth;
use school\models\Lang;
use school\models\Office;
use school\models\OfficeBook;
use school\models\searches\OfficeBookSearch;
use school\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * OfficeBookController implements the CRUD actions for OfficeBook model.
 */
class OfficeBookController extends Controller
{
    public function behaviors(): array
    {
        $rules = ['index', 'create', 'update', 'delete', 'autocomplete'];
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
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $searchModel = new OfficeBookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'languages'     => Lang::getLanguagesSimple(),
            'offices'       => Office::getOfficesListSimple(),
            'searchModel'   => $searchModel,
            'statuses'      => OfficeBook::getStatuses(),
            'userInfoBlock' => User::getUserInfoBlock(),
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
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }
        
        $model = new OfficeBook();

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (OfficeBook::find()->andWhere(['serial_number' => $model->serial_number, 'visible' => 1])->exists()) {
                Yii::$app->session->setFlash('error', 'Инвентарный номер должен быть уникальным.');
            } else {
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Учебник успешно добавлен.');
                    return $this->redirect(['office-book/index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось добавить учебник.');
                }
            }
        }

        return $this->render('create', [            
            'model'         => $model,
            'offices'       => Office::getOfficesListSimple(),
            'statuses'      => OfficeBook::getStatuses(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param $id
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
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (OfficeBook::find()->andWhere(['serial_number' => $model->serial_number, 'visible' => 1])->andWhere(['!=', 'id', $model->id])->exists()) {
                Yii::$app->session->setFlash('error', 'Инвентарный номер должен быть уникальным.');
            } else {
                if ($model->save(true, ['office_id', 'serial_number', 'year', 'status', 'comment'])) {
                    Yii::$app->session->setFlash('success', 'Учебник успешно изменен.');
                    return $this->redirect(['office-book/index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Не удалось изменить учебник.');
                }
            }
        }
        return $this->render('update', [            
            'model'         => $model,
            'offices'       => Office::getOfficesListSimple(),
            'statuses'      => OfficeBook::getStatuses(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * @param $id
     * @return mixed
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
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', 'Учебник успешно удален.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить учебник.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionAutocomplete()
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;
        if (!in_array($auth->roleId, [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Вам не разрешено производить данное действие.'));
        }

        $students = OfficeBook::getBooksAutocomplete(Yii::$app->request->post('term') ?? NULL);
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $students;
    }

    /**
     * @param integer $id
     * @return OfficeBook
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = OfficeBook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}