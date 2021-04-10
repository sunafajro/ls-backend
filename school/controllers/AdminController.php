<?php

namespace school\controllers;

use school\controllers\base\BaseController;
use school\models\AccessRule;
use school\models\searches\AccessRuleAssignmentSearch;
use school\models\searches\AccessRuleSearch;
use school\models\searches\EducationLevelSearch;
use school\models\searches\RoleSearch;
use Yii;
use yii\filters\AccessControl;

/**
 * Class AdminController
 * @package school\controllers
 */
class AdminController extends BaseController
{
    /** {@inheritDoc} */
    public function behaviors(): array
    {
        $rules = ['index', 'roles', 'education-levels', 'access-rules', 'access-rule-assignments'];
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
            'menuLinks' => self::getMenuLinks('access-rules'),
        ]);
    }

    /**
     * @return mixed
     */
    public function actionAccessRuleAssignments()
    {
        $this->layout = 'main-2-column';
        $searchModel = new AccessRuleAssignmentSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->get());

        return $this->render('access-rule-assignments', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'menuLinks' => self::getMenuLinks('access-rule-assignments'),
        ]);
    }

    /**
     * @param $key
     * @return array[]
     */
    private function getMenuLinks($key): array
    {
        $links = [];
        if (AccessRule::checkAccess('admin_roles')) {
            $links[] = [
                'url'     => 'admin/roles',
                'name'    => Yii::t('app','Roles'),
                'classes' => true,
                'active'  => $key === 'roles'
            ];
        }
        if (AccessRule::checkAccess('admin_education-levels')) {
            $links[] = [
                'url'     => 'admin/education-levels',
                'name'    => Yii::t('app','Education levels'),
                'classes' => true,
                'active'  => $key === 'education-levels'
            ];
        }
        if (AccessRule::checkAccess('admin_access-rules')) {
            $links[] = [
                'url'     => 'admin/access-rules',
                'name'    => Yii::t('app','Access rules'),
                'classes' => true,
                'active'  => $key === 'access-rules'
            ];
        }
        if (AccessRule::checkAccess('admin_access-rule-assignments')) {
            $links[] = [
                'url'     => 'admin/access-rule-assignments',
                'name'    => Yii::t('app','Access rule assignments'),
                'classes' => true,
                'active'  => $key === 'access-rule-assignments'
            ];
        }
        return $links;
    }
}