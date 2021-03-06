<?php

namespace school\controllers;

use Yii;
use school\models\KaslibroClient;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * KaslibroclientController implements the CRUD actions for KaslibroClient model.
 */
class KaslibroclientController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => false,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all KaslibroClient models.
     * @return mixed
     */
    public function actionIndex()
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=8) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model =  (new \yii\db\Query())
        ->select('id as id, name as name')
        ->from('calc_kaslibro_client')
        ->where('deleted=:zero', [':zero'=>0])
        ->all();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new KaslibroClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=8) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model = new KaslibroClient();

        if ($model->load(Yii::$app->request->post())) {
            $model->deleted = 0;
            $model->user = \Yii::$app->session->get('user.uid');
            $model->date = date('Y-m-d');
            $model->save();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing KaslibroClient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // всех кроме руководителей и бухгалтера редиректим обратно
        if(\Yii::$app->session->get('user.ustatus')!=3 && \Yii::$app->session->get('user.ustatus')!=8) { 
            return $this->redirect(\Yii::$app->request->referrer);
        }
        // всех кроме руководителей и бухгалтера редиректим обратно
        
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing KaslibroClient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the KaslibroClient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KaslibroClient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KaslibroClient::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
