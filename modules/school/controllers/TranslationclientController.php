<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Translationclient;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * TranslationclientController implements the CRUD actions for Translationclient model.
 */
class TranslationclientController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create','update','delete','disable'],
                'rules' => [
                    [
                        'actions' => ['create','update','disable'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','update','disable'],
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
     * Creates a new Translationclient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Translationclient();

        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->user = Yii::$app->session->get('user.uid');
            $model->data = date('Y-m-d');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый клиент успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить нового клиента!');
            }
            return $this->redirect(['translate/clients']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'userInfoBlock' => User::getUserInfoBlock(),
            ]);
        }
    }

    /**
     * Updates an existing Translationclient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные клиента успешно изменены!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить данные клиента!');
            }
            return $this->redirect(['translate/clients']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'userInfoBlock' => User::getUserInfoBlock(),
            ]);
        }
    }

    /**
    * Метод позволяет руководителям помечать клиентов как удаленные
    */

    public function actionDisable($id)
    {
        $model = $this->findModel($id);

        if($model->visible==1) {
            $model->visible = 0;
            $model->user_visible = Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Клиент успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить клиента!');
            }
        }
        return $this->redirect(['translate/clients']);
    }

    /**
     * Finds the Translationclient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Translationclient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Translationclient::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
