<?php

namespace school\controllers;

use Yii;
use school\models\Langtranslator;
use school\models\Translationlang;
use school\models\Translator;
use school\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * LangtranslatorController implements the CRUD actions for Langtranslator model.
 */
class LangtranslatorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create','delete'],
                'rules' => [
                    [
                        'actions' => ['create','delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','delete'],
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
				throw new ForbiddenHttpException('Access denied');
			}
			return true;
		} else {
			return false;
		}
	}
	
    /**
     * Метод позволяет руководителю добавить языки для переводчика.
     * Необходим ID переводчика.
     */
    public function actionCreate($tid)
    {
        $model = new Langtranslator();

        if ($model->load(Yii::$app->request->post())) {
            $model->calc_translator = $tid;
            $model->visible = 1;
            $model->user = Yii::$app->session->get('user.uid');
            $model->data = date('Y-m-d');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый язык успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новый язык!');
            }
            return $this->redirect(['langtranslator/create', 'tid'=>$tid]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'language'=> Translationlang::getLanguageListSimple(),
                'trlangs'=> Langtranslator::getTranslatorLanguagesById($tid),
                'translator'=> Translator::findOne($tid),
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        }
    }

    /**
     * Deletes an existing Langtranslator model.
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
                Yii::$app->session->setFlash('success', 'Язык успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить язык!');
            }
        }
        return $this->redirect(['langtranslator/create', 'tid' => $model->calc_translator]);
    }

    /**
     * Finds the Langtranslator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Langtranslator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Langtranslator::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
