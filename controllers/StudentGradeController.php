<?php

namespace app\controllers;

use Yii;
use app\models\AccessRule;
use app\models\Student;
use app\models\StudentGrade;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StudentGradeController extends Controller
{
    public function behaviors()
    {
        $rules = ['index', 'create', 'update', 'delete', 'download-attestation', 'exam-contents'];
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $rules,
                'rules' => [
                    [
                        'actions' => $rules,
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => $rules,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
	{
		if(parent::beforeAction($action)) {
            $rule = new AccessRule();
			if ($rule->checkAccess($action->controller->id, $action->id) === false) {
				throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
			}
			return true;
		} else {
			return false;
		}
    }
    
    public function actionIndex($id)
    {
        $student = Student::find()->andWhere(['id' => $id, 'visible' => 1])->one();
        if (empty($student)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new StudentGrade();
        $exams = StudentGrade::getExams();

        return $this->render('index', [
            'contentTypes'  => StudentGrade::getExamContentTypes(),
            'grades'        => StudentGrade::getStudentGrades(intval($id)),
            'exams'         => array_merge([NULL => Yii::t('app', 'Select an exam')], $exams),
            'model'         => $model,
            'student'       => $student,
            'userInfoBlock' => User::getUserInfoBlock()
        ]);
    }

    public function actionCreate($id)
    {
        $grade = new StudentGrade();
        $student = Student::findOne(intval($id));
        if ($student !== NULL) {
            $grade->load(Yii::$app->request->post());
            $grade->calc_studname = $id;
            if ($grade->save()) {
                $grade->writePdfFile();
                Yii::$app->session->setFlash('success', "Аттестация успешно добавлена!");
            } else {
                Yii::$app->session->setFlash('error', "Ошибка добавления аттестации!");
            }
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdate($id)
    {
        $grade = $this->findModel($id);
        $grade->load(Yii::$app->request->post());
        if ($grade->save(true, ['date', 'description', 'score', 'contents'])) {
            $grade->writePdfFile();
            Yii::$app->session->setFlash('success', "Аттестация успешно обновлена!");
        } else {
            Yii::$app->session->setFlash('error', "Ошибка добавления аттестации!");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDelete($id)
    {
        $grade = $this->findModel($id);
        if ($grade->delete()) {
            Yii::$app->session->setFlash('success', "Аттестация успешно удалена!");
        } else {
            Yii::$app->session->setFlash('error', "Ошибка удаления аттестации!");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDownloadAttestation($id)
    {
        /** @var StudentGrade $grade */
        $grade = $this->findModel($id);
        $filePath = $grade->getFullFileName();
        if (!file_exists($filePath)) {
            $grade->writePdfFile();
        }
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return Yii::$app->response->sendFile($filePath, null, ['inline' => true]);
    }

    public function actionExamContents($exam)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return StudentGrade::getExamContents($exam);
    }

    /**
     * Finds the StudentGrade model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentGrade the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentGrade::find()->andWhere(['id' => $id, 'visible' => 1])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}