<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Translationclient;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Class TranslationclientController
 * @package school\controllers
 */
class TranslationclientController extends BaseController
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = ['create','update','delete'];
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
                    'delete'   => ['post'],
                ],
            ],
        ];
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
    public function actionDelete($id)
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
