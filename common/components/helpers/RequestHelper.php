<?php

namespace common\components\helpers;

use Yii;

/**
 * Class RequestHelper
 * @package common\components\helpers
 */
class RequestHelper
{
    /**
     * @param array $params
     * @return array
     */
    public static function addCsrfToParams(array $params = []): array
    {
        $params[Yii::$app->request->csrfParam] = Yii::$app->request->getCsrfToken();

        return $params;
    }

    /**
     * @param array $options
     * @param array $params
     * @param string $confirm
     *
     * @return array
     */
    public static function createLinkPostOptions(array $options = [], array $params = [], string $confirm = ''): array
    {
        $options['data-method'] = 'POST';
        $options['data-params'] = self::addCsrfToParams($params);

        if (!empty($confirm)) {
            $options['confirm'] = $confirm;
        }

        return $options;
    }
}