<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Breadcrumbs extends Model
{
    private static function createBreadcrumbItem($name)
    {
        return [
            'text' => Yii::t('app', $name)
        ];
    }

    private static function createBreadcrumbItemWithUrl($name, $url)
    {
        return [
            'text' => Yii::t('app', $name),
            'to' => $url
        ];
    }

    public static function getItems()
    {
        return [
            'status' => true,
            'breadcrumbs' => [
                '/' => [
                    static::createBreadcrumbItem('Front')
                ],
                '/admin' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Administration')
                ],
                '/news' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('News')
                ],
                '/reports' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Reports')
                ],
                '/messages' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Messages')
                ],
                '/schedule' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Schedule')
                ],
                '/calls' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Calls')
                ],
                '/students' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Students')
                ],
                '/teachers' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Teachers')
                ],
                '/services' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Services')
                ],
                '/sales' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Sales')
                ],
                '/translations' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Translations')
                ],
                '/references' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('References')
                ],
                '/users' => [
                    static::createBreadcrumbItemWithUrl('Front', '/'),
                    static::createBreadcrumbItem('Users')
                ]
            ]
        ];
//   home: {
//     title: "Главная",
//     path: "/",
//     children: {
//       login: {
//         title: "Вход",
//         path: "/login"
//       },
//       admin: {
//         title: "Администрирование",
//         path: "/admin"
//       },
//       news: {
//         title: "Новости",
//         path: "/news"
//       },
//       report: {
//         title: "Отчеты",
//         path: "/reports"
//       },
//       messages: {
//         title: "Сообщения",
//         path: "/messages",
//       },
//       schedule: {
//         title: "Расписание",
//         path: "/schedule",
//       },
//       calls: {
//         title: "Звонки",
//         path: "/calls",
//       },
//       students: {
//         title: "Студенты",
//         path: "/students",
//       },
//       teachers: {
//         title: "Преподаватели",
//         path: "/teachers",
//       },
//       sale: {
//         title: "Услуги",
//         path: "/services"
//       },
//       sale: {
//         title: "Скидки",
//         path: "/sales"
//       },
//       reference: {
//         title: "Справочники",
//         path: "/references"
//       },
//       reference: {
//         title: "Переводы",
//         path: "/translations"
//       },
//       reference: {
//         title: "Пользователи",
//         path: "/users"
//       }
//     }
    }
}