<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Message;
use app\models\Salestud;

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
            'url' => '/admin/index',
            'classes' => 'fa fa-wrench',
            'title' => Yii::t('app', 'Administration'),
            'hasBadge' => false
        ];
        }
        /* ссылка на главную страничку со списком новостей */
        $menu[] = [
            'id' => 'news',
            'url' => '/site/index',
            'classes' => 'fa fa-newspaper-o',
            'title' => Yii::t('app', 'News'),
            'hasBadge' => false
        ];
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 6 ||
           (int)Yii::$app->session->get('user.ustatus') === 8 ||
           (int)Yii::$app->session->get('user.uid') === 296) {
            /* ссылка на раздел Отчеты */
            $menu[] = [
                'id' => 'reports',
                'url' => '/report/index', 
                'classes' => 'fa fa-list-alt',
                'title' => Yii::t('app', 'Reports'),
                'hasBadge' => false
            ];
        }

        if ((int)Yii::$app->session->get('user.ustatus') === 11) {
            /* ссылка на раздел Оплаты */
            $menu[] = [
                'id'       => 'payments',
                'url'      => '/moneystud/create',
                'classes'  => 'fa fa-rub',
                'title'    => Yii::t('app', 'Payments'),
                'hasBadge' => false
            ];
        }

        if ((int)Yii::$app->session->get('user.ustatus') !== 2 &&
           (int)Yii::$app->session->get('user.ustatus') !== 8 &&
           (int)Yii::$app->session->get('user.ustatus') !== 9 &&
           (int)Yii::$app->session->get('user.ustatus') !== 11) {
            /* ссылка на раздел Расписание */
            $menu[] = [
                'id' => 'schedule',
                'url' => '/schedule/index',
                'classes' => 'fa fa-calendar',
                'title' => Yii::t('app', 'Schedule'),
                'hasBadge' => false
            ];
        }

        if (Yii::$app->params['appMode'] === 'standalone') {
            /* ссылка на раздел Сообщения */
            $menu[] = [
                'id' => 'messages',
                'url' => '/message/index',
                'classes' => 'fa fa-envelope',
                'title' => Yii::t('app', 'Messages'),
                'hasBadge' => true,
                'cnt' => Message::getMessagesCount()
            ];
        }

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4) {
            /* ссылка на раздел Группы */
            $menu[] = [
                'id' => 'groups',
                'url' => '/groupteacher/index',
                'classes' => 'fa fa-users',
                'title' => Yii::t('app', 'Groups'),
                'hasBadge' => false
            ];
        }

        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 5 ||
           (int)Yii::$app->session->get('user.ustatus') === 6) {
            /* ссылка на раздел Звонки */
            $menu[] = [
                'id' => 'calls',
                'url' => '/call/index',
                'classes' => 'fa fa-phone',
                'title' => Yii::t('app', 'Calls'),
                'hasBadge' => false
            ];
            /* ссылка на раздел Клиенты */
            $menu[] = [
                'id' => 'clients',
                'url' => '/studname/index',
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
                'url' => '/teacher/index',
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
                'url' => '/service/index',
                'classes' => 'fa fa-shopping-cart',
                'title' => Yii::t('app', 'Services'),
                'hasBadge' => false
            ];
            /* ссылка на раздел Скидки */
            $menu[] = [
                'id' => 'sales',
                'url' => '/sale/index',
                'classes' => 'fa fa-gift',
                'title' => Yii::t('app', 'Sales'),
                'hasBadge' => true,
                'cnt' => Salestud::getSalesCount()
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4 ||
           (int)Yii::$app->session->get('user.ustatus') === 7) {
            /* ссылка на раздел Учебники */
            $menu[] = [
                'id' => 'books',
                'url' => '/book/index',
                'classes' => 'fa fa-book',
                'title' => Yii::t('app', 'Books'),
                'hasBadge' => false,
                'cnt' => false
            ];
        }        
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 4) {
            /* ссылка на раздел Скидки */
            $menu[] = [
                'id' => 'documents',
                'url' => '/document/index',
                'classes' => 'fa fa-files-o',
                'title' => Yii::t('app', 'Documents'),
                'hasBadge' => false,
                'cnt' => false
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3 ||
           (int)Yii::$app->session->get('user.ustatus') === 9) {
            /* ссылка на раздел Переводы */
            $menu[] = [
                'id' => 'translations',
                'url' => '/translate/translations',
                'classes' => 'fa fa-retweet',
                'title' => Yii::t('app', 'Translations'),
                'hasBadge' => false
            ];
        }
        if((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uid') === 296) {
            /* ссылка на раздел Пользователи */
            $menu[] = [
                'id' => 'users',
                'url' => '/user/index', 
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
                'url' => '/reference/index',
                'classes' => 'fa fa-cog',
                'title' => Yii::t('app', 'References'),
                'hasBadge' => false
            ];
        }

        /* ссылка на метод выхода */
        $menu[] = [
            'id' => 'logout',
            'url' => '/site/logout',
            'classes' => 'fa fa-sign-out',
            'title' => Yii::t('app', 'Logout'),
            'post' => true,
            'hasBadge' => false
        ];

        return [
            'navElements' => $menu,
            'message' => Message::getLastUnreadMessage(),
            'sale' => Salestud::getLastUnapprovedSale(),
        ];
    }
}
