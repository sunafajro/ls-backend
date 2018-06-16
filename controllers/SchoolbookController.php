<?php

namespace app\controllers;

use Yii;
use app\models\Schoolbook;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * SchoolbookController implements the CRUD actions for Schoolbook model.
 */
class SchoolbookController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
                'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'update'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create', 'delete', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new Schoolbook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->session->get('user.ustatus') == 3) {
            $this->layout = 'column2';
            $model = new Schoolbook();

            $languages_tmp = (new \yii\db\Query())
            ->select('id as id, name as name')
            ->from('calc_lang')
            ->where('visible=:one', [':one'=>1])
            ->orderby(['name'=>SORT_ASC])
            ->all();

            if(!empty($languages_tmp)) {
                foreach($languages_tmp as $l) {
                    $languages[$l['id']] = $l['name'];
                }
            } else {
                $languages = [];
            }

            if ($model->load(Yii::$app->request->post())) {
                $model->visible = 1;
                $model->save();
                return $this->redirect(['site/reference', 'type'=>12]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'languages' => $languages,
                ]);
            }
        } else {
            return $this->redirect(['site/reference', 'type'=>12]);
        }

    }

    /**
     * Updates an existing Schoolbook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->session->get('user.ustatus') == 3) {
            $this->layout = 'column2';
            $model = $this->findModel($id);

            $languages_tmp = (new \yii\db\Query())
            ->select('id as id, name as name')
            ->from('calc_lang')
            ->where('visible=:one', [':one'=>1])
            ->orderby(['name'=>SORT_ASC])
            ->all();

            if(!empty($languages_tmp)) {
                foreach($languages_tmp as $l) {
                    $languages[$l['id']] = $l['name'];
                }
            } else {
                $languages = [];
            }


            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['site/reference', 'type'=>12]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'languages' => $languages,
                ]);
            }
        } else {
            return $this->redirect(['site/reference', 'type'=>12]);
        }
    }

    /**
     * Deletes an existing Schoolbook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(Yii::$app->session->get('user.ustatus') == 3) {
            $model = $this->findModel($id);
            if($model->visible == 1) {
                $model->visible = 0;
                $model->save();
            }
        }
        return $this->redirect(['site/reference', 'type'=>12]);
    }

    /**
     * Finds the Schoolbook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Schoolbook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Schoolbook::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
