<?php

namespace exam\controllers;

use exam\Exams;
use exam\models\forms\UserForm;
use exam\models\Role;
use exam\models\search\UserSearch;
use exam\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        $rules = ['index', 'create', 'update', 'delete'];
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
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'roles'        => Role::find()->select('name')->indexBy('id')->column(),
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $userForm = new UserForm();
        $userForm->scenario = UserForm::SCENARIO_CREATE;

        if (Yii::$app->request->isPost) {
            if ($userForm->load(Yii::$app->request->post())) {
                if ($userForm->save()) {
                    Yii::$app->session->setFlash('success', "Успешно создан пользователь #{$userForm->id}.");

                    return $this->redirect(['user/index']);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось создать пользователя.");
                }
            }
        }

        return $this->render('create', [
            'roles'    => Role::find()->select('name')->indexBy('id')->column(),
            'userForm' => $userForm,
        ]);
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $userForm = UserForm::loadFromModel($user);
        $userForm->scenario = UserForm::SCENARIO_UPDATE;

        if (Yii::$app->request->isPost) {
            if ($userForm->load(Yii::$app->request->post())) {
                if ($userForm->save()) {
                    Yii::$app->session->setFlash('success', "Пользователь #{$userForm->id} успешно изменен.");

                    return $this->redirect(['user/index']);
                } else {
                    Yii::$app->session->setFlash('error', "Не удалось изменить пользователя #{$userForm->id}.");
                }
            }
        }

        return $this->render('update', [
            'roles'    => Role::find()->select('name')->indexBy('id')->column(),
            'userForm' => $userForm,
        ]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id)
    {
        $user = $this->findModel($id);
        if ($user->delete()) {
            Yii::$app->session->setFlash('success', "Пользователь #{$user->id} успешно удален.");
        } else {
            Yii::$app->session->setFlash('error', "Не удалось удалить пользователя #{$user->id}.");
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var User|null $model */
        if (($model = User::find()->byId($id)->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
