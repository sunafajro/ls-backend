<?php

namespace school\controllers;

use school\models\AccessRule;
use Yii;
use school\models\Role;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * RoleController implements the CRUD actions for Status model.
 */
class RoleController extends Controller
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
     * @return mixed
     * @throws \Exception
     */
    public function actionCreate()
    {
        $model = new Role();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Роль успешно создана.');
                return $this->redirect(['admin/roles']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось создать роль.');
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
        $model = $this->findModel(intval($id));

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Роль успешно изменена.');
                return $this->redirect(['admin/roles']);
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось изменить роль.');
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
            Yii::$app->session->setFlash('success', 'Роль успешно удалена.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить роль.');
        }

        return $this->redirect(['admin/roles']);
    }

    /**
     * @param integer $id
     * @return Role
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Role
    {
        if (($model = Role::find()->byId($id)->active()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
