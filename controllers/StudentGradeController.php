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
use kartik\mpdf\Pdf;

class StudentGradeController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'delete', 'download-attestation', 'exam-contents'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'delete', 'download-attestation', 'exam-contents'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'create','delete', 'download-attestation', 'exam-contents'],
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
        $model = new StudentGrade();
        $student = Student::findOne(intval($id));
        $exams = StudentGrade::getExams();
        $examsAll = array_merge([NULL => Yii::t('app', 'Select an exam')], $exams);
        if ($student !== NULL) {
            return $this->render('index', [
                'contentTypes'  => StudentGrade::getExamContentTypes(),
                'grades'        => StudentGrade::getStudentGrades(intval($id)),
                'exams'         => $examsAll,
                'model'         => $model,
                'student'       => $student,
                'userInfoBlock' => User::getUserInfoBlock()
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreate($id)
    {
        $model = new StudentGrade();
        $student = Student::findOne(intval($id));
        if ($student !== NULL) {
            $model->load(Yii::$app->request->post());
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
            $filePath = Yii::getAlias("@attestates/{$grade->calc_studname}/attestate-{$id}.pdf");
            if ((int)$grade->visible === 1) {
                $grade->visible = 0;
                if ($grade->save(true, ['visible'])) {
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
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
        $attestation = StudentGrade::getAttestation(intval($id));
        if ($attestation) {
            $attestationsDirPath = Yii::getAlias("@attestates");
            $attestationsByStudentDirPath = "{$attestationsDirPath}/{$attestation['studentId']}";
            $filePath = "$attestationsByStudentDirPath/attestate-{$id}.pdf";
            if (!file_exists($filePath)) {
                if (!file_exists($attestationsDirPath)) {
                    mkdir($attestationsDirPath, 0775, true);
                    if (!file_exists($attestationsByStudentDirPath)) {
                        mkdir($attestationsByStudentDirPath, 0775, true);
                    }
                }
                $pdf = new Pdf([
                    'filename'    => $filePath,
                    'mode'        => Pdf::MODE_UTF8,
                    'format'      => Pdf::FORMAT_A4,
                    'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'destination' => Pdf::DEST_FILE, 
                    'content'     => $this->renderPartial('viewPdf', [
                        'attestation'  => $attestation,
                        'contentTypes' => StudentGrade::getExamContentTypes(),
                        'exams'        => StudentGrade::getExams(),
                    ]),
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
                $pdf->render();
            }
            
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionExamContents($exam)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return StudentGrade::getExamContents($exam);
    }
}