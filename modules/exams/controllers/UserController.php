<?php

namespace app\modules\exams\controllers;

use app\modules\exams\Exams;
use app\modules\exams\models\User;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        $rules = ['view'];
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
            ]
        ];
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $user = $this->findModel($id);

        return $this->render('view', []);
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
        if (($model = User::find()->andWhere(['id' => $id, 'module_type' => Exams::MODULE_NAME])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
