<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use Yii;
use school\models\Student;
use school\models\StudentGrade;
use school\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Class StudentGradeController
 * @package school\controllers
 */
class StudentGradeController extends BaseController
{
    public function behaviors(): array
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

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($id)
    {
        $this->layout = 'main-2-column';

        /** @var \school\models\Auth $auth */
        $auth = \Yii::$app->user->identity;

        $student = Student::findOne(['id' => $id, 'visible' => 1]);
        if (empty($student)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new StudentGrade();
        if ($auth->roleId === 4) {
            $model->office_id = $auth->officeId;
        }
        $exams = StudentGrade::getExams();

        return $this->render('index', [
            'contentTypes'  => StudentGrade::getExamContentTypes(),
            'grades'        => StudentGrade::getStudentGrades(intval($id)),
            'exams'         => array_merge([NULL => Yii::t('app', 'Select an exam')], $exams),
            'model'         => $model,
            'student'       => $student,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($id)
    {
        $grade = new StudentGrade();
        $student = Student::findOne(['id' => $id, 'visible' => 1]);
        if (empty($student)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $grade->load(Yii::$app->request->post());
        $grade->calc_studname = $id;
        if ($grade->save()) {
            $grade->writePdfFile();
            Yii::$app->session->setFlash('success', "Аттестация успешно добавлена!");
        } else {
            Yii::$app->session->setFlash('error', "Ошибка добавления аттестации!");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $grade = $this->findModel((int)$id);
        $grade->load(Yii::$app->request->post());
        if ($grade->save(true, ['date', 'description', 'score', 'contents', 'teacher_id', 'office_id'])) {
            $grade->writePdfFile();
            Yii::$app->session->setFlash('success', "Аттестация успешно обновлена!");
        } else {
            Yii::$app->session->setFlash('error', "Ошибка добавления аттестации!");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $grade = $this->findModel((int)$id);
        if ($grade->delete()) {
            Yii::$app->session->setFlash('success', "Аттестация успешно удалена!");
        } else {
            Yii::$app->session->setFlash('error', "Ошибка удаления аттестации!");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDownloadAttestation($id)
    {
        $grade = $this->findModel((int)$id);
        $filePath = $grade->getFullFileName();
        if (!file_exists($filePath)) {
            $grade->writePdfFile();
        }
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return Yii::$app->response->sendFile($filePath, null, ['inline' => true]);
    }

    /**
     * @param $exam
     * @return mixed
     */
    public function actionExamContents($exam)
    {
        return $this->asJson(StudentGrade::getExamContents($exam));
    }

    /**
     * @param integer $id
     * @return StudentGrade
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): StudentGrade
    {
        if (($model = StudentGrade::find()->byId($id)->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}