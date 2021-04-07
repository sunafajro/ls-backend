<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\News;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
		    'access' => [
                'class' => AccessControl::class,
                'only' => ['create','delete','update'],
                'rules' => [
                    [
                        'actions' => ['create','delete','update'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','delete','update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
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
        $model = new News();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новость успешно добавлена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новость!');
            }
            return $this->redirect(['site/index']);
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
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новость успешно изменена!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить новость!');
            }
            return $this->redirect(['site/index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->visible = 0;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Новость успешно удалена!');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить новость!');
        }

        return $this->redirect(['site/index']);
    }

    /**
     * @param integer $id
     * @return News
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
