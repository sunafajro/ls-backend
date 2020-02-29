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
        $grade = new StudentGrade();
        $student = Student::findOne(intval($id));
        if ($student !== NULL) {
            $grade->load(Yii::$app->request->post());
            $grade->calc_studname = $id;
            if ($grade->save()) {
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
            Yii::$app->session->setFlash('success', "Аттестация успешно обновлена!");
            $filePath = Yii::getAlias("@attestates/{$grade->calc_studname}/attestate-{$id}.pdf");
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } else {
            Yii::$app->session->setFlash('error', "Ошибка добавления обновлена!");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDelete($id)
    {
        $grade = $this->findModel($id);
        if ((int)$grade->visible === 1) {
            $grade->visible = 0;
            if ($grade->save(true, ['visible'])) {
                $filePath = Yii::getAlias("@attestates/{$grade->calc_studname}/attestate-{$id}.pdf");
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

    /**
     * Finds the StudentGrade model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentGrade the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentGrade::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}