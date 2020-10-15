<?php

namespace app\models;

use yii\base\Model;

/**
 * Модель горизонтального меню навигации
 *
 * Class BaseNavigation
 * @package app\models
 */
class BaseNavigation extends Model
{
    /**
     * Получение списка элементов меню
     *
     * @return array
     */
    public function getItems()
    {
        return [];
    }
}