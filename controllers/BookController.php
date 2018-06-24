<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * BookController implements the CRUD actions for CalcBook model.
 */
class BookController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','view','create','update','delete','disable'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','delete','disable'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','view','create','update','disable'],
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
     * Lists all CalcBook models.
     * @return mixed
     */
    public function actionIndex()
    {
	$this->layout = "column2";
       /* $searchModel = new CalcBookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams); */
        $books = (new \yii\db\Query())
        ->select('cb.id as bid, cb.name as bname, cb.isbn as isbn, cbp.name as bpname, cl.name as lname, cbil.num as bcount, cbpr.price as bprice')
        ->from('calc_book cb')
        ->leftJoin('calc_bookprice cbpr','cb.id=cbpr.calc_book')
        ->leftJoin('calc_lang cl','cl.id=cb.calc_lang')
        ->leftJoin('calc_bookpublisher cbp','cbp.id=cb.calc_bookpublisher')
        ->leftJoin('calc_bookincomelist cbil','cbil.calc_book=cb.id')
        ->where('cb.visible=:vis and cbpr.visible=:vis and cbil.visible=:vis',[':vis'=>1])
        ->orderBy(['cb.visible'=>SORT_DESC,'cb.calc_bookpublisher'=>SORT_ASC,'cb.calc_lang'=>SORT_ASC])
        ->all();


        return $this->render('index', [
         /*   'searchModel' => $searchModel,
            'dataProvider' => $dataProvider, */
            'books'=>$books,
        ]);
    }

    /**
     * Displays a single CalcBook model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CalcBook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CalcBook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
     * Deletes an existing CalcBook model.
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
     * Finds the CalcBook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcBook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
