<?php

namespace app\modules\exams\models;

use app\models\BaseNavigation;
use Yii;
use app\models\Message;
use app\models\Salestud;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class Navigation
 * @package app\modules\exams\models
 */
class Navigation extends BaseNavigation
{
    /**
     * @return array
     */
    public function getItems()
    {
        $roleId = (int)Yii::$app->user->identity->roleId;

        /* создаем пустые массивы */
        $menu = [];

        //if (in_array($roleId, [1])) {
            /* ссылка на раздел Пользователи */
            $menu[] = [
                'url'      => Url::to(['user/index']),
                'label'    => Html::tag('i', '', ['class' => 'fa fa-user']) . Yii::t('app', 'Users'),
                'encode'   => false,
            ];
        //}

        /* ссылка на метод выхода */
        $menu[] = [
            'url'         => Url::to(['site/logout']),
            'label'       => Html::tag('i', '', ['class' => 'fa fa-sign-out']) . Yii::t('app', 'Logout'),
            'linkOptions' => ['data-method' => 'post'],
            'encode'      => false,
        ];

        return [
            'navElements' => $menu,
            'message'     => null,
        ];
    }
}
