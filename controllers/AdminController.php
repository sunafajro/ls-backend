<?php

namespace app\controllers;

use Yii;
use app\models\Role;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class AdminController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','roles'],
                'rules' => [
                    [
                        'actions' => ['index','roles'],
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index','roles'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            if (User::checkAccess($action->controller->id, $action->id) === false) {
                throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
            }
            return true;
        } else {
            return false;
        }
    }

    public function actionIndex()
    {
        return $this->redirect(['roles']);
    }

    /* метод выводит таблицу ролей */
    public function actionRoles()
    {
        return $this->render('roles', [
            'roles' => Role::getRolesList(),
            'links' => self::getList('roles'),
            'userInfoBlock' => User::getUserInfoBlock(),
        ]);
    }

    protected function getList($key)
    {
        $links = [
            [
                'url'     => 'admin/roles',
                'name'    => Yii::t('app','Roles'),
                'classes' => true,
                'active'  => ($key === 'roles' ? true : false)
            ]
        ];

        return $links;
    }
}