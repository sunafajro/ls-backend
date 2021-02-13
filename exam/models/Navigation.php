<?php

namespace exam\models;

use common\components\helpers\IconHelper;
use common\components\helpers\RequestHelper;
use common\models\BaseNavigation;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Navigation
 * @package exam\models
 */
class Navigation extends BaseNavigation
{
    /**
     * @return array
     */
    public function getItems()
    {
        /* создаем пустые массивы */
        $menu = [];

        $menu[] = [
            'url'      => Url::to(['user/index']),
            'label'    => IconHelper::icon('users', Yii::t('app', 'Users'), 'fa5'),
            'encode'   => false,
        ];

        /* ссылка на метод выхода */
        $menu[] = [
            'url'         => Url::to(['site/logout']),
            'label'       => IconHelper::icon('sign-out-alt', Yii::t('app', 'Logout'), 'fa5'),
            'linkOptions' => RequestHelper::createLinkPostOptions(),
            'encode'      => false,
        ];

        return [
            'navElements' => $menu,
            'message'     => null,
        ];
    }
}
