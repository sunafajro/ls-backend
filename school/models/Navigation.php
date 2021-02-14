<?php

namespace school\models;

use common\models\BaseNavigation;
use Yii;
use yii\helpers\Url;

/**
 * Class Navigation
 * @package school\models
 */
class Navigation extends BaseNavigation
{
    /**
     * @return array
     */
    public function getItems()
    {
        $roleId = (int)Yii::$app->session->get('user.ustatus');
        $userId = (int)Yii::$app->session->get('user.uid');

        /* создаем пустые массивы */
        $menu = [];
        /* ссыдка на страничку администрирования */
        if ($roleId === 3){
        $menu[] = [
            'id' => 'admin',
            'url' => Url::to(['admin/index']),
            'classes' => 'fa fa-wrench',
            'title' => Yii::t('app', 'Administration'),
            'hasBadge' => false
        ];
        }
        /* ссылка на главную страничку со списком новостей */
        $menu[] = [
            'id' => 'news',
            'url' => Url::to(['site/index']),
            'classes' => 'fa fa-newspaper-o',
            'title' => Yii::t('app', 'News'),
            'hasBadge' => false
        ];
        if (in_array($roleId, [3, 4, 6, 8]) || $userId === 296) {
            /* ссылка на раздел Отчеты */
            $menu[] = [
                'id' => 'reports',
                'url' => Url::to(['report/index']),
                'classes' => 'fa fa-list-alt',
                'title' => Yii::t('app', 'Reports'),
                'hasBadge' => false
            ];
        }

        if ($roleId === 11) {
            /* ссылка на раздел Оплаты */
            $menu[] = [
                'id'       => 'payments',
                'url'      => Url::to(['moneystud/create']),
                'classes'  => 'fa fa-rub',
                'title'    => Yii::t('app', 'Payments'),
                'hasBadge' => false
            ];
        }

        if (!in_array($roleId, [2, 8, 9, 11])) {
            /* ссылка на раздел Расписание */
            $menu[] = [
                'id' => 'schedule',
                'url' => Url::to(['schedule/index']),
                'classes' => 'fa fa-calendar',
                'title' => Yii::t('app', 'Schedule'),
                'hasBadge' => false
            ];
        }

        if (Yii::$app->params['appMode'] === 'standalone') {
            /* ссылка на раздел Сообщения */
            $menu[] = [
                'id' => 'messages',
                'url' => Url::to(['message/index']),
                'classes' => 'fa fa-envelope',
                'title' => Yii::t('app', 'Messages'),
                'hasBadge' => true,
                'cnt' => Message::getMessagesCount()
            ];
        }

        /* ссылка на раздел Задачи */
//        $menu[] = [
//            'id' => 'tasks',
//            'url' => Url::to(['ticket/index']),
//            'classes' => 'fa fa-tasks',
//            'title' => Yii::t('app', 'Tickets'),
//            'hasBadge' => true,
//            'cnt' => Ticket::getTasksCount()
//        ];


        if (in_array($roleId, [3, 4])) {
            /* ссылка на раздел Группы */
            $menu[] = [
                'id' => 'groups',
                'url' => Url::to(['groupteacher/index']),
                'classes' => 'fa fa-users',
                'title' => Yii::t('app', 'Groups'),
                'hasBadge' => false
            ];
        }

        if (in_array($roleId, [3, 4, 5, 6])) {
            /* ссылка на раздел Звонки */
            $menu[] = [
                'id' => 'calls',
                'url' => Url::to(['call/index']),
                'classes' => 'fa fa-phone',
                'title' => Yii::t('app', 'Calls'),
                'hasBadge' => false
            ];
            /* ссылка на раздел Клиенты */
            $menu[] = [
                'id' => 'clients',
                'url' => ['studname/index'],
                'classes' => 'fa fa-graduation-cap',
                'title' => Yii::t('app', 'Clients'),
                'hasBadge' => false
            ];
        }
        if (in_array($roleId, [3, 4, 5, 6, 8, 10])) {
            /* ссылка на раздел Преподаватели */   
            $menu[] = [
                'id' => 'teachers',
                'url' => Url::to(['teacher/index']),
                'classes' => 'fa fa-suitcase',
                'title' => Yii::t('app', 'Teachers'),
                'hasBadge' => false
            ];
        }
        if (in_array($roleId, [3, 4])) {
            /* ссылка на раздел Услуги */
            $menu[] = [
                'id' => 'services',
                'url' => ['service/index'],
                'classes' => 'fa fa-shopping-cart',
                'title' => Yii::t('app', 'Services'),
                'hasBadge' => false
            ];
        }
        if (in_array($roleId, [3, 4]) || $userId === 389) {
            /* ссылка на раздел Скидки */
            $menu[] = [
                'id' => 'sales',
                'url' => Url::to(['sale/index']),
                'classes' => 'fa fa-gift',
                'title' => Yii::t('app', 'Sales'),
                'hasBadge' => true,
                'cnt' => Salestud::getSalesCount()
            ];
        }
        if (in_array($roleId, [3, 4, 7])) {
            /* ссылка на раздел Учебники */
            $menu[] = [
                'id' => 'books',
                'url' => Url::to(['book/index']),
                'classes' => 'fa fa-book',
                'title' => Yii::t('app', 'Books'),
                'hasBadge' => false,
                'cnt' => false
            ];
        }        
        if (in_array($roleId, [3, 4])) {
            /* ссылка на раздел Скидки */
            $menu[] = [
                'id' => 'documents',
                'url' => Url::to(['document/index']),
                'classes' => 'fa fa-files-o',
                'title' => Yii::t('app', 'Documents'),
                'hasBadge' => false,
                'cnt' => false
            ];
        }
        if (in_array($roleId, [3, 9])) {
            /* ссылка на раздел Переводы */
            $menu[] = [
                'id' => 'translations',
                'url' => Url::to(['translate/translations']),
                'classes' => 'fa fa-retweet',
                'title' => Yii::t('app', 'Translations'),
                'hasBadge' => false
            ];
        }
        if ($roleId === 3 || $userId === 296) {
            /* ссылка на раздел Пользователи */
            $menu[] = [
                'id' => 'users',
                'url' => Url::to(['user/index']),
                'classes' => 'fa fa-user',
                'title' => Yii::t('app', 'Users'),
                'hasBadge' => false
            ];
        }

        if (in_array($roleId, [3, 4, 9])) {
            /* ссылка на раздел Справочники */
            $menu[] = [
                'id' => 'references',
                'url' => Url::to(['references/index']),
                'classes' => 'fa fa-cog',
                'title' => Yii::t('app', 'References'),
                'hasBadge' => false
            ];
        }

        /* ссылка на метод выхода */
        $menu[] = [
            'id' => 'logout',
            'url' => Url::to(['site/logout']),
            'classes' => 'fa fa-sign-out',
            'title' => Yii::t('app', 'Logout'),
            'post' => true,
            'hasBadge' => false
        ];

        return [
            'navElements' => $menu,
            'message'     => Message::getLastUnreadMessage(),
        ];
    }
}
