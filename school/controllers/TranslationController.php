<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Translation;
use school\models\Translationlang;
use school\models\Translationclient;
use school\models\Translationnorm;
use school\models\Translator;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * Class TranslationController
 * @package school\controllers
 */
class TranslationController extends BaseController
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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

    /**
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';

        $model = new Translation();
        $model->data = date('Y-m-d');
        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            $model->user = Yii::$app->session->get('user.uid');            
            if (($n = Translationnorm::findOne($model->calc_translationnorm)) !== null) {
                $model->value = $model->accunitcount * $n->value;
            }
            $model->value = $model->value + $model->value_correction;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Новый перевод успешно добавлен!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось добавить новый перевод!');
            }
            return $this->redirect(['translate/translations']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'languages' => Translationlang::getLanguageListSimple(),
                'clients' => Translationclient::getClientListSimple(),
                'translators' => Translator::getTranslatorListSimple(),
                'norms' => Translationnorm::getNormListSimple(),
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
                'model' => $model,
                'languages' => Translationlang::getLanguageListSimple(),
                'clients' => Translationclient::getClientListSimple(),
                'translators' => Translator::getTranslatorListSimple(),
                'norms' => Translationnorm::getNormListSimple(),
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
                Yii::$app->session->setFlash('success', 'Перевод успешно удален!');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось удалить перевод!');
            }
		}
		return $this->redirect(['translate/translations']);
    }

    /**
     * @param integer $id
     * @return Translation
     * @throws NotFoundHttpException
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
