<?php

namespace school\controllers;

use school\models\AccessRule;
use school\models\Role;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class AccessRuleController
 * @package school\controllers
 */
class AccessRuleController extends \yii\web\Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $rules = ['create','update','delete'];
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
                    ]
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
        if (parent::beforeAction($action)) {
            if (AccessRule::checkAccess($action->controller->id, $action->id) === false) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';
        $model = new AccessRule();

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Правило доступа успешно создано.');
                return $this->redirect(['admin/access-rules']);
            } else {
                \Yii::$app->session->setFlash('error', 'Не удалось создать правило доступа.');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id)
    {
        $this->layout = 'main-2-column';
        $model = $this->findModel(intval($id));

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Правило доступа успешно изменено.');
                return $this->redirect(['admin/access-rules']);
            } else {
                \Yii::$app->session->setFlash('error', 'Не удалось изменить правило доступа.');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete(string $id)
    {
        if ($this->findModel(intval($id))->delete()) {
            \Yii::$app->session->setFlash('success', 'Правило доступа успешно удалено.');
        } else {
            \Yii::$app->session->setFlash('error', 'Не удалось удалить правло доступа.');
        }

        return $this->redirect(['admin/access-rules']);
    }

    /**
     * @param integer $id
     * @return AccessRule
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): AccessRule
    {
        if (($model = AccessRule::find()->byId($id)->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}