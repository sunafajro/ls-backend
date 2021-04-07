<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Translationnorm;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Class TranslationnormController
 * @package school\controllers
 */
class TranslationnormController extends BaseController
{
    public function behaviors(): array
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

    /**
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';

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
                Yii::$app->session->setFlash('success', 'Норма оплаты успешно изменена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить норму оплаты!');
            }
            return $this->redirect(['translate/norms']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * @param integer $id
     * @return Translationnorm
     * @throws NotFoundHttpException
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
