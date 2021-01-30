<?php

namespace app\modules\school\controllers;

use app\modules\school\models\AccessRule;
use app\models\search\EducationLevelSearch;
use app\modules\school\models\search\RoleSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Class AdminController
 * @package app\modules\school\controllers
 */
class AdminController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $rules = ['index', 'roles', 'education-levels'];
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
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
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
     */
    public function actionIndex()
    {
        return $this->redirect(['roles']);
    }

    /**
     * @return mixed
     */
    public function actionRoles()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('roles', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'menuLinks' => self::getMenuLinks('roles'),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionEducationLevels()
    {
        $searchModel = new EducationLevelSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('education-levels', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'menuLinks' => self::getMenuLinks('education-levels'),
        ]);
    }

    /**
     * @param $key
     * @return array[]
     */
    private function getMenuLinks($key): array
    {
        return [
            [
                'url'     => 'admin/roles',
                'name'    => Yii::t('app','Roles'),
                'classes' => true,
                'active'  => $key === 'roles'
            ],
            [
                'url'     => 'admin/education-levels',
                'name'    => Yii::t('app','Education levels'),
                'classes' => true,
                'active'  => $key === 'education-levels'
            ]
        ];
    }
}