<?php

namespace app\controllers;

use Yii;
use app\models\Student;
use app\models\StudentGrade;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StudentGradeController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'delete'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post']
                ],
            ],
        ];
    }

    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
			if (User::checkAccess($action->controller->id, $action->id) === false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
    }
    
    public function actionIndex($id)
    {
        $model = new StudentGrade();
        $student = Student::findOne($id);
        if ($student !== NULL) {
            return $this->render('index', [
                'grades' => $model->getStudentGrades(['sid' => $id]),
                'gradeTypes' => StudentGrade::getGradeTypes(),
                'model' => $model,
                'student' => $student,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreate($id)
    {
        $model = new StudentGrade();
        $student = Student::findOne($id);
        if ($student !== NULL) {
            $model->load(Yii::$app->request->post());
            $model->user = Yii::$app->session->get('user.uid');
            $model->calc_studname = $id;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', "Аттестация успешно добавлена!");
                $model = new StudentGrade();
            } else {
                Yii::$app->session->setFlash('error', "Ошибка добавления аттестации!");
            }
            return $this->redirect(['index', 'id' => $id]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete($id)
    {
        $grade = StudentGrade::findOne($id);
        if ($grade !== NULL) {
            if ((int)$grade->visible === 1) {
                $grade->visible = 0;
                if ($grade->save()) {
                    Yii::$app->session->setFlash('success', "Аттестация успешно удалена!");
                } else {
                    Yii::$app->session->setFlash('error', "Ошибка удаления аттестации!");
                }
            } else {
                Yii::$app->session->setFlash('error', "Аттестация не является действующей!");
            }
            return $this->redirect(['index', 'id' => $grade->calc_studname]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}