<?php


namespace exam\controllers;

use exam\components\managers\interfaces\SpeakingExamManagerInterface;
use exam\models\SpeakingExam;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class SpeakingExamController
 * @package exam\controllers
 */
class SpeakingExamController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $rules = ['index', 'view', 'create', 'update', 'delete'];
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
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var SpeakingExamManagerInterface $orderManager */
        $speakingExamManager = \Yii::$container->get(SpeakingExamManagerInterface::class);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $speakingExamManager->getExams(),
            'sort' => [
                'defaultOrder' => 'id',
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView(int $id)
    {
        /** @var SpeakingExamManagerInterface $orderManager */
        $speakingExamManager = \Yii::$container->get(SpeakingExamManagerInterface::class);

        $examModel = $speakingExamManager->getExamById($id);
        return $this->render('view', [
            'examModel' => $examModel,
        ]);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function actionChange(int $id)
    {
        /** @var SpeakingExamManagerInterface $orderManager */
        $speakingExamManager = \Yii::$container->get(SpeakingExamManagerInterface::class);

        $examModel = $speakingExamManager->getExamById($id);
        if ($examModel->load(\Yii::$app->request->post()) && $examModel->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'The exam was successfully updated.'));
        } else {
            \Yii::$app->session->setFlash('success', \Yii::t('app', 'Failed to update the exam.'));
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }
}