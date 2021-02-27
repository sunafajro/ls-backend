<?php

namespace client\controllers;

use client\models\File;
use client\models\searches\GroupFileSearch;
use client\models\searches\LessonSearch;
use Yii;
use client\models\forms\ChangeUsernameForm;
use client\models\forms\ChangePasswordForm;
use client\models\Message;
use client\models\Student;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class StudentController
 * @package client\controllers
 */
class StudentController extends Controller {
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $rules = ['profile', 'payments', 'courses', 'lessons', 'attestations', 'download-attestation', 'messages', 'settings'];
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
        ];
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionProfile()
    {
        $student = Student::findOne(Yii::$app->user->id);
        if ($student) {
            $lessons = $student->getPassedLessonsByService();
            $serviceIds = [];
            foreach ($lessons as $lesson) {
                $serviceIds[] = $lesson['serviceId'];
            }
            $services = $student->getOrderedLessonsByService($serviceIds);
            $balance = $student->calculateBalance(
                $student->debt,
                $services,
                $lessons
            );
            $schedule = $student->getSchedule();
            $teachers = $student->getTeachers();
            return $this->render('profile', [
                'balance'  => $balance,
                'lessons'  => $lessons,
                'schedule' => $schedule,
                'services' => $services,
                'student'  => $student,
                'teachers' => $teachers,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPayments()
    {
        $student = Student::findOne(Yii::$app->user->id);
        if ($student) {
            $payments = $student->getPayments();
            return $this->render('payments', [
                'payments' => $payments,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCourses()
    {
        $student = $this->findModel(Yii::$app->user->id);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $student->getCourses()
        ]);
        return $this->render('courses', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param string $id
     * @param string $type
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCourse(string $id, string $type = 'lessons')
    {
        $student = $this->findModel(Yii::$app->user->id);

        $searchModel = null;
        $dataProvider = null;
        switch($type) {
            case 'announcements':
                break;
            case 'files':
                $groupIds = $student->getGroupsByCourseId(intval($id));
                $groupIds = ArrayHelper::getColumn($groupIds, 'id');
                $searchModel = new GroupFileSearch(['entityId' => $groupIds]);
                $dataProvider = $searchModel->search(\Yii::$app->request->get());
                break;
            default:
                $searchModel = new LessonSearch(['serviceId' => intval($id), 'studentId' => $student->id]);
                $dataProvider = $searchModel->search(\Yii::$app->request->get());
        }

        return $this->render('course', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $student->getCourses(intval($id))[0] ?? [],
            'tab' => $type
        ]);
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAttestations()
    {
        $student = $this->findModel(Yii::$app->user->id);
        $attestations = $student->getAttestations();
        return $this->render('attestations', [
            'contentTypes' => Student::getExamContentTypes(),
            'grades'       => $attestations,
            'exams'        => Student::getExams(),
        ]);
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionMessages()
    {
        $student = $this->findModel(Yii::$app->user->id);
        $messageForm = new Message();
        $messages = $student->getMessages();
        $receivers = $student->availableMessageReceiversList();
        if (Yii::$app->request->isPost && $messageForm->load(Yii::$app->request->post())) {
            $files = [];
            if (is_array($messageForm->files) && !empty($messageForm->files)) {
                $files = $messageForm->files;
                $messageForm->files = '';
            }
            if ($messageForm->save()) {
                foreach ($files ?? [] as $fileId) {
                    $file = File::find()->andWhere(['id' => $fileId])->one();
                    $file->setEntity(File::TYPE_MESSAGE_FILES, $messageForm->id);
                }
                $messageReport = (new \yii\db\Query())
                ->createCommand()
                ->insert(
                    'calc_messreport',
                    [
                        'calc_message' => $messageForm->id,
                        'user'         => $messageForm->refinement_id,
                        'ok'           => 0,
                        'data'         => date('Y-m-d H:i:s'),
                        'send'         => 1
                    ]
                )->execute();;
                Yii::$app->session->setFlash('success', Yii::t('app', 'Message successfully sent'));
                return $this->redirect(['student/messages']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to send message'));
            }
        }
        return $this->render('messages', [
            'messages'    => $messages,
            'messageForm' => $messageForm,
            'receivers'   => $receivers,
        ]);
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionSettings()
    {
        $this->findModel(Yii::$app->user->id);
        $changeUsername = new ChangeUsernameForm();
        $changePassword = new ChangePasswordForm();
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('ChangeUsernameForm')) {
                if ($changeUsername->load(Yii::$app->request->post()) && $changeUsername->save()) {
                    $changeUsername = new ChangeUsernameForm();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Login parameters successfully updated'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to update login parameters'));
                }
            }
            if (Yii::$app->request->post('ChangePasswordForm')) {
                if ($changePassword->load(Yii::$app->request->post()) && $changePassword->save()) {
                    $changePassword = new ChangePasswordForm();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Login parameters successfully updated'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to update login parameters'));
                }
            }
        }
        return $this->render('settings', [
            'changeUsername' => $changeUsername,
            'changePassword' => $changePassword,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDownloadAttestation($id)
    {
        $student = $this->findModel(Yii::$app->user->id);
        $attestation = $student->getAttestation($id);
        if ($attestation) {
            $filePath = Yii::getAlias("@attestates/{$student->id}/attestate-{$id}.pdf");
            if (!file_exists($filePath)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Student the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Student
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}