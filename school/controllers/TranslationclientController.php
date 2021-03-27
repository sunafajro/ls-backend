<?php

namespace school\controllers;

use Yii;
use school\models\Translationclient;
use school\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Class TranslationclientController
 * @package school\controllers
 */
class TranslationclientController extends Controller
{
    /** {@inheritDoc} */
    public function behaviors(): array
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

    /** {@inheritDoc} */
	public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
			if (User::checkAccess($action->controller->id, $action->id) == false) {
				throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
			}
			return true;
		} else {
			return false;
		}
	}
	
    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';
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
            ]);
        }
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $this->layout = 'main-2-column';
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
            ]);
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * @param integer $id
     * @return Translationclient
     * @throws NotFoundHttpException
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
