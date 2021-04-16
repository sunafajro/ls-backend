<?php

namespace school\controllers\base;

use common\models\queries\BaseActiveQuery;
use school\models\AccessRule;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * Class BaseController
 * @package school\controllers\base
 */
class BaseController extends Controller
{
    /**
     * {@inheritDoc}
     * @throws \yii\web\BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            if (AccessRule::checkAccess("{$action->controller->id}_{$action->id}") === false) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $entityClass
     * @return array
     */
    protected function getEntityItems($entityClass): array
    {
        /** @var BaseActiveQuery $query */
        $query = call_user_func([$entityClass, 'find']);
        return $query
            ->select(['name'])
            ->active()
            ->indexBy('id')
            ->orderBy(['name' => SORT_ASC])
            ->column();
    }
}