<?php

namespace app\models;

use yii\base\Model;

/**
 * Модель горизонтального меню навигации
 *
 * Class Navigation
 * @package app\models
 */
class Navigation extends Model
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