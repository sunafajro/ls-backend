<?php

namespace app\modules\school\controllers;

use app\models\Lang;
use app\models\Office;
use app\models\OfficeBook;
use app\models\User;
use app\models\search\OfficeBookSearch;
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
    public function behaviors()
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

    public function actionIndex()
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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

    public function actionCreate()
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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

    public function actionUpdate($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
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

    public function actionDelete($id)
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', 'Учебник успешно удален.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить учебник.');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAutocomplete()
    {
        if (!in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4, 7])) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }

        $students = OfficeBook::getBooksAutocomplete(Yii::$app->request->post('term') ?? NULL);
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $students;
    }

    /**
     * Finds the OfficeBook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OfficeBook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
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