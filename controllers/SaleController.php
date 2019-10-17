<?php

namespace app\controllers;

use Yii;
use app\models\Sale;
use app\models\search\DiscountSearch;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * SaleController implements the CRUD actions for CalcSale model.
 */
class SaleController extends Controller
{
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
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
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

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            if ((int)Yii::$app->session->get('user.uid') === 389 && $action->id === 'index') {
                return true;
            }
            if (User::checkAccess($action->controller->id, $action->id) == false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Выводим основную страничку с React приложением Скидки.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DiscountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /** 
     * Creates discount
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sale();
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if (!Sale::find()->andWhere(['value' => $model->value, 'procent' => $model->procent, 'visible' => 1])->exists()) {
                $sale = Sale::createSale($model->name, $model->procent, $model->value, $model->base);
                if ($sale > 0) {
                    Yii::$app->session->setFlash('success', 'Скидка успешно создана');
                    return $this->redirect(['sale/index']);
                } else {
                    Yii::$app->session->setFlash('success', 'Не удалось создать скидку');
                }
            } else {
                Yii::$app->session->setFlash('success', 'Скидка уже существует');
            }
        }
        
        return $this->render('create', [
            'model'         => $model,
            'types'         => Sale::getTypeLabels(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * Updates discount
     * @param int $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (empty($model)) {
            throw new NotFoundHttpException('Скидка не найдена');
        }
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save(true, ['name'])) {
                Yii::$app->session->setFlash('success', 'Скидка успешно обновлена');
                return $this->redirect(['sale/index']);
            } else {
                Yii::$app->session->setFlash('success', 'Не удалось обновить скидку');
            }
        }
        
        return $this->render('update', [
            'model'         => $model,
            'types'         => Sale::getTypeLabels(),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    /**
     * Deletes discount
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = Sale::findOne($id);
        if (empty($model)) {
            throw new NotFoundHttpException('Скидка не найдена');
        }
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Скидка успешно удалена'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Не удалось удалить скидку'));
        }
        
        return $this->redirect(['sale/index']);
    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
