<?php

namespace app\modules\school\controllers;

use Yii;
use app\models\Translationlang;
use app\modules\school\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * TranslationlangController implements the CRUD actions for Translationlang model.
 */
class TranslationlangController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
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
     * Creates a new Translationlang model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
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
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Updates an existing Translationlang model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Deletes an existing Translationlang model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
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
     * Finds the Translationlang model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Translationlang the loaded model
     * @throws NotFoundHttpException if the model cannot be found
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
