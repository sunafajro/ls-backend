<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\News;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
		    'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'delete', 'index', 'update'],
                'rules' => [
                    [
                        'actions' => ['create', 'delete', 'index', 'update'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create', 'delete', 'index', 'update'],
                        'allow' => true,
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

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = ['month' => date('m'), 'year' => date('Y')];
        if (Yii::$app->request->get('mon') && 
           (int)Yii::$app->request->get('mon') >= 1 && 
           (int)Yii::$app->request->get('month') <= 12) {
                $params['month'] = (int)Yii::$app->request->get('month');
        }
        if (Yii::$app->request->get('year') && 
           (int)Yii::$app->request->get('year') >= 2011) {
                $params['year'] = (int)Yii::$app->request->get('year');
        }
        return [
            'actions' => AccessRule::GetCRUD('news'),
            'news'    => News::getNewsList($params['month'], $params['year']),
            'status'  => true,			
	    ];
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if(Yii::$app->session->get('user.ustatus') != 3) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $userInfoBlock = User::getUserInfoBlock();
        $model = new News();

        if ($model->load(Yii::$app->request->post())) {
			$model->visible = 1;
			$model->author = Yii::$app->session->get('user.uid');
			$model->date = date('Y-m-d H:i:s');
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Новость успешно добавлена!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось добавить новость!');
            }
            return $this->redirect(['site/index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'userInfoBlock' => $userInfoBlock
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->session->get('user.ustatus') != 3) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $userInfoBlock = User::getUserInfoBlock();
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Новость успешно изменена!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось изменить новость!');
            }
            return $this->redirect(['site/index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'userInfoBlock' => $userInfoBlock
            ]);
        }
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if($model !== NULL) {
            $model->visible = 0;
            if($model->save()) {
                // если успешно, задаем сообщение об успешности
                Yii::$app->session->setFlash('success', 'Новость успешно удалена!');
            } else {
                // если не успешно задаем сообщение об ошибке
                Yii::$app->session->setFlash('error', 'Не удалось удалить новость!');
            }
        }

        return $this->redirect(['site/index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
