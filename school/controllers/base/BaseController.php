<?php

namespace school\controllers\base;

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
            if (AccessRule::checkAccess($action->controller->id, $action->id) === false) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие.');
            }
            return true;
        } else {
            return false;
        }
    }
}