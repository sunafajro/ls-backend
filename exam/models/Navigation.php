<?php

namespace exam\models;

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
            'label'    => Html::tag('i', '', ['class' => 'fas fa-users']) . ' ' . Yii::t('app', 'Users'),
            'encode'   => false,
        ];

        /* ссылка на метод выхода */
        $menu[] = [
            'url'         => Url::to(['site/logout']),
            'label'       => Html::tag('i', '', ['class' => 'fas fa-sign-out-alt']) . ' ' . Yii::t('app', 'Logout'),
            'linkOptions' => ['data-method' => 'post'],
            'encode'      => false,
        ];

        return [
            'navElements' => $menu,
            'message'     => null,
        ];
    }
}
