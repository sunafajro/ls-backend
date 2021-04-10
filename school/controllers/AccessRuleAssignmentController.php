<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use school\models\AccessRuleAssignment;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Class AccessRuleAssignmentController
 * @package school\controllers
 */
class AccessRuleAssignmentController extends BaseController
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
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $this->layout = 'main-2-column';
        $model = new AccessRuleAssignment();

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                \Yii::$app->session->setFlash('success', 'Назначение правила доступа успешно создано.');
                return $this->redirect(['admin/access-rule-assignments']);
            } else {
                \Yii::$app->session->setFlash('error', 'Не удалось создать назначение правила доступа.');
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
                \Yii::$app->session->setFlash('success', 'Назначение правила доступа успешно изменено.');
                return $this->redirect(['admin/access-rule-assignments']);
            } else {
                \Yii::$app->session->setFlash('error', 'Не удалось изменить назначение правила доступа.');
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
            \Yii::$app->session->setFlash('success', 'Назначение правила доступа успешно удалено.');
        } else {
            \Yii::$app->session->setFlash('error', 'Не удалось удалить назначение правила доступа.');
        }

        return $this->redirect(['admin/access-rule-assignments']);
    }

    /**
     * @param integer $id
     * @return AccessRuleAssignment
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): AccessRuleAssignment
    {
        if (($model = AccessRuleAssignment::find()->byId($id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}