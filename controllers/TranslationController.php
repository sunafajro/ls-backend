<?php

namespace app\controllers;

use Yii;
use app\models\Translation;
use app\models\Translationlang;
use app\models\Translationclient;
use app\models\Translationnorm;
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
 * TranslationController implements the CRUD actions for Translation model.
 */
class TranslationController extends Controller
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
                    ]
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
     * Метод позволяет руководителям внести в таблицу запись о переводе
     */
    public function actionCreate()
    {
        $model = new Translation();
        $model->data = date('Y-m-d');
        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->user = Yii::$app->session->get('user.uid');            
            if(($n = Translationnorm::findOne($model->calc_translationnorm)) !== null) {
                $model->value = $model->accunitcount * $n->value;
            }
            $model->value = $model->value + $model->value_correction;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый перевод успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новый перевод!');
            }
            return $this->redirect(['translate/translations']);
        } else {
            return $this->render('create', [
                'userInfoBlock' => User::getUserInfoBlock(),
                'model' => $model,
                'language' => Translationlang::getLanguageListSimple(),
                'client' => Translationclient::getClientListSimple(),
                'translator' => Translator::getTranslatorListSimple(),
                'norm' => Translationnorm::getNormListSimple(),
            ]);
        }
    }

    /**
     * Updates an existing Translation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->data_end === '0000-00-00') {
            $model->data_end = date('Y-m-d');
        }
        if ($model->load(Yii::$app->request->post())) {
            if(($n = Translationnorm::findOne($model->calc_translationnorm)) !== null) {
                $model->value = $model->accunitcount * $n->value;
            }
            $model->value = $model->value + $model->value_correction;
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Перевод успешно изменен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить перевод!');
            }
            return $this->redirect(['translate/translations']);
        } else {
            return $this->render('update', [
                'userInfoBlock' => User::getUserInfoBlock(),
                'model' => $model,
                'language' => Translationlang::getLanguageListSimple(),
                'client' => Translationclient::getClientListSimple(),
                'translator' => Translator::getTranslatorListSimple(),
                'norm' => Translationnorm::getNormListSimple(),
            ]);
        }
    }

    /**
    * Метод позволяет руководителям помечать переводы как удаленные
    */

    public function actionDisable($id)
    {
		$model = $this->findModel($id);

		if($model->visible==1) {
			$model->visible = 0;
			$model->user_visible = Yii::$app->session->get('user.uid');
			$model->data_visible = date('Y-m-d');
            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Перевод успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить перевод!');
            }
		}
		return $this->redirect(['translate/translations']);
    }

    /**
     * Finds the Translation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Translation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Translation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
