<?php

namespace app\controllers;

use Yii;
use app\models\Langteacher;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * LangteacherController implements the CRUD actions for CalcLangteacher model.
 */
class LangteacherController extends Controller
{
    public function behaviors()
    {
        return [
		'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','delete','disable'],
                'rules' => [
                    [
                        'actions' => ['create','delete','disable'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['create','disable'],
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

    public function actionCreate($tid)
    {
        if(Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus') != 4){
            return $this->redirect(Yii::$app->request->referrer);            
        }
		//указываем двухколоночный дизайн
		$this->layout = 'column2';
		//проверяем что такой преподаватель есть
		$teacher = (new \yii\db\Query())
		->select('id as tid, name as tname')
		->from('calc_teacher')
		->where('visible=:vis and id=:id',[':vis'=>1,':id'=>Yii::$app->request->get('tid')])
		->one();
	
		//если массив не пустой, продолжаем
		if(!empty($teacher)) {
            $model = new Langteacher();
            //если пришли данные запросом post и валидация успешная редиректим обратно на страницу
            if ($model->load(Yii::$app->request->post())) {
                if($model->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app','Новый язык успешно добавлен!'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app','Не удалось добавить новый язык!'));
                }
                return $this->redirect(['create', 'tid' => $model->calc_teacher]);
            }
			//формируем массив с языками преподавателя
			$tlangs = (new \yii\db\Query())
			->select('clt.id as clid, ct.name as tname, clt.calc_teacher as tid, cl.id as lid, cl.name as lname, u.name as uname, clt.data as cldate')
			->from('calc_langteacher clt')
			->leftJoin('calc_lang cl','cl.id=clt.calc_lang')
			->leftJoin('calc_teacher ct','ct.id=clt.calc_teacher')
			->leftJoin('user u','u.id=clt.user')
			->where('clt.visible=:vis and clt.calc_teacher=:tid',[':vis'=>1,':tid'=>Yii::$app->request->get('tid')])
			->orderBy(['cl.name'=>SORT_ASC])
			->all();
		
			//формируем массив доступных языков
			$slangs = (new \yii\db\Query())
			->select('cl.id as lid, cl.name as lname')
			->from('calc_lang cl')
			->where('cl.visible=:vis',[':vis'=>1])
			->orderBy(['cl.name'=>SORT_ASC])
			->all();

			return $this->render('create', [
				'model' => $model,
				'slangs' => $slangs,
				'tlangs' => $tlangs,
				'teacher' => $teacher,
			]);
		} else {
            Yii::$app->session->setFlash('error', Yii::t('app','Не удалось найти преподавателя!'));
			return $this->redirect(['teacher/index']);
		}
	}

    public function actionDisable($id)
    {
        if(Yii::$app->session->get('user.ustatus')!=3 && Yii::$app->session->get('user.ustatus') != 4){
            return $this->redirect(Yii::$app->request->referrer);            
        }
        // получаем информацию по пользователю
        $model=$this->findModel($id);
        //проверяем текущее состояние
        if($model->visible==1) {
            $model->visible = 0;
                if($model->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app','Язык успешно удален!'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app','Не удалось удалить язык!'));
                }
            }
        return $this->redirect(['create','tid'=>Yii::$app->request->get('tid')]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CalcLangteacher model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CalcLangteacher the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Langteacher::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
