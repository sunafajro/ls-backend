<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Translator;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Class TranslatorController
 * @package school\controllers
 */
class TranslatorController extends BaseController
{
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
        ];
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';

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
            ]);
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $this->layout = 'main-2-column';

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
            ]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
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
     * @param integer $id
     * @return Translator
     * @throws NotFoundHttpException
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
