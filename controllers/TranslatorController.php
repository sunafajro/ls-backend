<?php

namespace app\controllers;

use Yii;
use app\models\Translator;
use app\models\Tool;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * TranslatorController implements the CRUD actions for Translator model.
 */
class TranslatorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','update','disable'],
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
     * Creates a new Translator model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Translator();

        if ($model->load(Yii::$app->request->post())) {
            $model->lname = trim($model->lname);
            $model->fname = trim($model->fname);
            $model->name = $model->lname." ".$model->fname;
            if($model->mname){
                $model->mname = trim($model->mname);
                $model->name .= " ".$model->mname;
            }
            $model->phone = trim($model->phone);
            $model->email = trim($model->email);
            $model->skype = trim($model->skype);
            $model->url = trim($model->url);
            $model->visible = 1;
            $model->user = Yii::$app->session->get('user.uid');
            $model->data = date('Y-m-d');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый переводчик успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить нового переводчика!');
            }
            return $this->redirect(['translate/translators']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Updates an existing Translator model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->lname = trim($model->lname);
            $model->fname = trim($model->fname);
            $model->name = $model->lname." ".$model->fname;
            if($model->mname){
                $model->mname = trim($model->mname);
                $model->name .= " ".$model->mname;
            }
            $model->phone = trim($model->phone);
            $model->email = trim($model->email);
            $model->skype = trim($model->skype);
            $model->url = trim($model->url);
            $model->save();            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные переводчика успешно изменены!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить данные переводчика!');
            }
            return $this->redirect(['translate/translators']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
    * Метод позволяет руководителям помечать переводчиков как удаленные
    */

    public function actionDisable($id)
    {
        $model = $this->findModel($id);

        if($model->visible !=0 ) {
            $model->visible = 0;
            $model->user_visible = Yii::$app->session->get('user.uid');
            $model->data_visible = date('Y-m-d');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Переводчик успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить переводчика!');
            }
        }
        return $this->redirect(['translate/translators']);
    }

    /**
     * Finds the Translator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Translator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Translator::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
