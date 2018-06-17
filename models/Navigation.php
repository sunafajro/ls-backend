<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Kaslibro;
use app\models\Message;
use app\models\Salestud;
use app\models\Ticket;

/**
 * Модель горизонтального меню навигации
 */
class Navigation extends Model
{
    public static function getItems()
    {
        /* создаем пустые массивы */
        $menu = [];
        /* ссыдка на страничку администрирования */
        if ((int)Yii::$app->session->get('user.ustatus') === 3){
        $menu[] = [
            'id' => 'admin',
            'url' => '/admin',
            'classes' => 'fa fa-wrench',
            'title' => Yii::t('app', 'Administration'),
            'hasBadge' => false
        ];
        }
        /* ссылка на главную страничку со списком новостей */
        $menu[] = [
            'id' => 'news',
            'url' => '/news',
            'classes' => 'fa fa-newspaper-o',
            'title' => Yii::t('app', 'News'),
            'hasBadge' => false
        ];
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 8) {
            /* ссылка на раздел Отчеты */
            $menu[] = [
                'id' => 'reports',
                'url' => '/reports', 
                'classes' => 'fa fa-list-alt',
                'title' => Yii::t('app', 'Reports'),
                'hasBadge' => false
            ];
            /* ссылка на раздел Расходы */
            // $menu[] = [
            //     'id' => 'expenses',
            //     'url' => '/kaslibro/index',
            //     'classes' => 'fa fa-money',
            //     'title' => Yii::t('app', 'Expenses'),
            //     'hasBadge' => true,
            //     'cnt' => Kaslibro::getExpensesCount()
            // ];
        }

        if((int)Yii::$app->session->get('user.ustatus') !== 2 &&
           (int)Yii::$app->session->get('user.ustatus') !== 8 &&
           (int)Yii::$app->session->get('user.ustatus') !== 9) {
            /* ссылка на раздел Расписание */
            $menu[] = [
                'id' => 'schedule',
                'url' => '/schedule',
                'classes' => 'fa fa-calendar',
                'title' => Yii::t('app', 'Schedule'),
                'hasBadge' => false
            ];
        }

        /* ссылка на раздел Задачи */
        // $menu[] = [
        //     'id' => 'tasks',
        //     'url' => '/ticket/index',
        //     'classes' => 'fa fa-tasks',
        //     'title' => Yii::t('app', 'Tickets'),
        //     'hasBadge' => true,
        //     'cnt' => Ticket::getTasksCount()
        // ];

        /* ссылка на раздел Сообщения */
        $menu[] = [
            'id' => 'messages',
            'url' => '/messages',
            'classes' => 'fa fa-envelope',
            'title' => Yii::t('app', 'Messages'),
            'hasBadge' => true,
            'cnt' => Message::getMessagesCount()
        ];

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 5 ||
           (int)Yii::$app->session->get('user.ustatus') === 6) {
            /* ссылка на раздел Звонки */
            $menu[] = [
                'id' => 'calls',
                'url' => '/calls',
                'classes' => 'fa fa-phone',
                'title' => Yii::t('app', 'Calls'),
                'hasBadge' => false
            ];
            /* ссылка на раздел Клиенты */
            $menu[] = [
                'id' => 'clients',
                'url' => '/students',
                'classes' => 'fa fa-graduation-cap',
                'title' => Yii::t('app', 'Clients'),
                'hasBadge' => false
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 5 ||
           (int)Yii::$app->session->get('user.ustatus') === 6 ||
           (int)Yii::$app->session->get('user.ustatus') === 8 ||
           (int)Yii::$app->session->get('user.ustatus') === 10) {
            /* ссылка на раздел Преподаватели */   
            $menu[] = [
                'id' => 'teachers',
                'url' => '/teachers',
                'classes' => 'fa fa-suitcase',
                'title' => Yii::t('app', 'Teachers'),
                'hasBadge' => false
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4) {
            /* ссылка на раздел Услуги */
            $menu[] = [
                'id' => 'services',
                'url' => '/services',
                'classes' => 'fa fa-shopping-cart',
                'title' => Yii::t('app', 'Services'),
                'hasBadge' => false
            ];
            /* ссылка на раздел Скидки */
            $menu[] = [
                'id' => 'sales',
                'url' => '/sales',
                'classes' => 'fa fa-gift',
                'title' => Yii::t('app', 'Sales'),
                'hasBadge' => true,
                'cnt' => Salestud::getSalesCount()
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 9) {
            /* ссылка на раздел Переводы */
            $menu[] = [
                'id' => 'translations',
                'url' => '/translations',
                'classes' => 'fa fa-retweet',
                'title' => Yii::t('app', 'Translations'),
                'hasBadge' => false
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3) {
            /* ссылка на раздел Пользователи */
            $menu[] = [
                'id' => 'users',
                'url' => '/users', 
                'classes' => 'fa fa-user',
                'title' => Yii::t('app', 'Users'),
                'hasBadge' => false
            ];
        }

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 9) {
            /* ссылка на раздел Справочники */
            $menu[] = [
                'id' => 'references',
                'url' => '/references',
                'classes' => 'fa fa-book',
                'title' => Yii::t('app', 'References'),
                'hasBadge' => true
            ];
        }

        $sale = Salestud::getLastUnapprovedSale();
        if (!empty($sale)) {
            $sale['title'] = 'Подтвердить скидку для клиента.';
        }
        return [
            'navigation' => $menu,
            'message' => Message::getLastUnreadMessage(),
            'task' => Ticket::getLastUnreadTask(),
            'sale' => $sale
        ];
    }

    public static function getCounters()
    {
        $sale = Salestud::getLastUnapprovedSale();
        if (!empty($sale)) {
            $sale['title'] = 'Подтвердить скидку для клиента.';
        }
        return [
            'message' => Message::getLastUnreadMessage(),
            'task' => Ticket::getLastUnreadTask(),
            'sale' => $sale,
            'cnts' => [
                'messages' => Message::getMessagesCount(),
                'tasks' => Ticket::getTasksCount(),
                'sales' => Salestud::getSalesCount(),
            ]
        ];
    }
}
