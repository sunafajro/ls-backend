<?php

namespace app\modules\school\controllers;

use app\modules\school\models\User;
use Yii;
use app\models\Develop;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * DevelopController implements the CRUD actions for Develop model.
 */
class DevelopController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','update','delete','enable','disable','close'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete','disable','close'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','create','update','disable','close'],
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Develop models.
     * @return mixed
     */
    public function actionIndex()
    {
        $ut = User::tableName();
        $model = (new \yii\db\Query())
        ->select('dev.id as id, dev.creation_date as creation_date, u1.name as creation_user, dev.description as description, dev.type as type, dev.status as status, dev.severity as severity, dev.close_date as close_date, u2.name as close_user')
        ->from('calc_develop dev')
        ->leftjoin(['u1' => $ut], 'u1.id = dev.creation_user')
        ->leftjoin(['u2' => $ut], 'u2.id = dev.close_user')
        ->where(['dev.visible'=>1])
        ->orderby(['dev.status' => SORT_ASC, 'dev.severity' => SORT_ASC, 'dev.creation_date' => SORT_DESC])
        ->all();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Develop model.
     * @param integer $id
     * @return mixed
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    */
    /**
     * Creates a new Develop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Develop();

        if ($model->load(Yii::$app->request->post())) {
            $model->creation_date = date('Y-m-d H:i:s');
            $model->creation_user = Yii::$app->session->get('user.uid');
            $model->status = 1;
            $model->visible = 1;
            $model->save();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Develop model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Develop model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionClose($id) {
        if(Yii::$app->session->get('user.uid')=='139') {
            $model = $this->findModel($id);
            if($model->status == 1) {
                $model->close_date = date('Y-m-d H:i:s');
                $model->close_user = Yii::$app->session->get('user.uid');
                $model->status = 2;
                $model->save();
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Develop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Develop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Develop::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
