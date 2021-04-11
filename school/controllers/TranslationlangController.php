<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Translationlang;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Class TranslationlangController
 * @package school\controllers
 */
class TranslationlangController extends BaseController
{
    /** {@inheritdoc} */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create','update','delete'],
                'rules' => [
                    [
                        'actions' => ['create','update','delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','update','delete'],
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
     * @throws \Exception
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';

        $model = new Translationlang();
        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->data = date('Y-m-d');
            $model->user = Yii::$app->session->get('user.uid');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый язык успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новый язык!');
            }
            return $this->redirect(['translate/languages']);
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
                Yii::$app->session->setFlash('success', 'Язык успешно изменен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить язык!');
            }
            return $this->redirect(['translate/languages']);
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
            $model->data_visible = date('Y-m-d');
            $model->user_visible = Yii::$app->session->get('user.uid');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Язык успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить язык!');
            }
        }

        return $this->redirect(['translate/languages']);
    }

    /**
     * @param integer $id
     * @return Translationlang
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Translationlang::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
