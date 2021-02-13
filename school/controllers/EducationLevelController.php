<?php

namespace school\controllers;

use school\models\EducationLevel;
use school\models\AccessRule;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EducationLevelController implements the CRUD actions for EducationLevel model.
 */
class EducationLevelController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $rules = ['create', 'update', 'delete'];
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @throws \yii\web\BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action): bool
    {
        if(parent::beforeAction($action)) {
            if (AccessRule::checkAccess($action->controller->id, $action->id) === false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView(string $id)
    {
        return $this->render('view', [
            'model' => $this->findModel(intval($id)),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EducationLevel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['admin/education-levels']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id)
    {
        $model = $this->findModel(intval($id));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['admin/education-levels']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(string $id)
    {
        if ($this->findModel(intval($id))->delete()) {
            Yii::$app->session->setFlash('success', 'Уровень обучения успешно удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить уровень обучения.');
        }

        return $this->redirect(['admin/education-levels']);
    }

    /**
     * @param int $id
     * @return EducationLevel
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): EducationLevel
    {
        if (($model = EducationLevel::find()->byId($id)->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
