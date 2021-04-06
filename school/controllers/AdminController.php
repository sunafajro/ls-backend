<?php

namespace school\controllers;

use school\models\AccessRule;
use school\models\searches\AccessRuleSearch;
use school\models\searches\EducationLevelSearch;
use school\models\searches\RoleSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Class AdminController
 * @package school\controllers
 */
class AdminController extends Controller
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = ['index', 'roles', 'education-levels', 'access-rules'];
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
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
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
        $this->layout = 'main-2-column';
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
        $this->layout = 'main-2-column';
        $searchModel = new EducationLevelSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('education-levels', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'menuLinks' => self::getMenuLinks('education-levels'),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionAccessRules()
    {
        $this->layout = 'main-2-column';
        $searchModel = new AccessRuleSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('access-rules', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'menuLinks' => self::getMenuLinks('roles'),
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
            ],
            [
                'url'     => 'admin/access-rules',
                'name'    => Yii::t('app','Access rules'),
                'classes' => true,
                'active'  => $key === 'access-rules'
            ]
        ];
    }
}