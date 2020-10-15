<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Translationnorm;
use app\modules\school\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * TranslationnormController implements the CRUD actions for Translationnorm model.
 */
class TranslationnormController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create','update','delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create','update','delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create','update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            if (User::checkAccess($action->controller->id, $action->id) == false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Метод позволяет рукоаодителям добавлять нормы оплаты по переводам
     */
    public function actionCreate()
    {
        $model = new Translationnorm();

        if ($model->load(Yii::$app->request->post())) {
            $model->user = Yii::$app->session->get('user.uid');
            $model->data = date('Y-m-d'); 
            $model->visible = 1;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Новая норма оплаты успешно добавлена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новую норму оплаты!');
            }
            return $this->redirect(['translate/norms']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Updates an existing Translationnorm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Норма оплаты успешно изменена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить норму оплаты!');
            }
            return $this->redirect(['translate/norms']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Deletes an existing Translationnorm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		
		if($model->visible != 0) {
			$model->visible = 0;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Норма оплаты успешно удалена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить норму оплаты!');
            }
		}

        return $this->redirect(['translate/norms']);
    }

    /**
     * Finds the Translationnorm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Translationnorm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Translationnorm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
