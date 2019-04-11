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
use kartik\mpdf\Pdf;

class StudentGradeController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'delete', 'download-attestation'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'delete', 'download-attestation'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create','delete', 'download-attestation'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['post'],
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
                'grades' => $model->getStudentGrades($id),
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

    public function actionDownloadAttestation($id)
    {
        $model = new StudentGrade();
        $attestation = $model->getAttestation($id);
        if ($attestation) {                
            $pdf = new Pdf([
                'mode'        => Pdf::MODE_UTF8,
                'format'      => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                'destination' => Pdf::DEST_BROWSER, 
                'content'     => $this->renderPartial('_viewPdf', ['attestation' => $attestation]),
                'cssFile'     => '@app/web/css/print_attestate.css',
                'options'     => [
                    'title'   => Yii::t('app', 'Attestation'),
                ],
                'marginHeader' => 0,
                'marginFooter' => 0,
                'marginTop'    => 0,
                'marginBottom' => 0,
                'marginLeft'   => 0,
                'marginRight'  => 0,
            ]);
            return $pdf->render();
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}