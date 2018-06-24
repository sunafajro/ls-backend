<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Модель меню ссылок раздела Справочники
 */
class Reference extends Model
{
    public static function getItems()
    {
        $links = [
            [
                'url'     => '/phonebook',
                'name'    => Yii::t('app','Phones'),
            ]
        ];
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $links[] = [
                'url'     => '/languages',
                'name'    => Yii::t('app','Languages'),
            ];
            $links[] = [
                'url'     => '/studentnorms',
                'name'    => Yii::t('app','Student norms'),
            ];
            $links[] = [
                'url'     => '/teachernorms',
                'name'    => Yii::t('app','Teacher norms'),
            ];
            $links[] = [ 
                'url'     => '/timenorms',
                'name'    => Yii::t('app','Time norms'),
            ];
            $links[] = [
                'url'     => '/premiums',
                'name'    => Yii::t('app','Language premiums'),
            ];
            $links[] = [
                'url'     => '/coefficients',
                'name'    => Yii::t('app','Accrual coefficients'),
            ];
            $links[] = [
                'url'     => '/cities',
                'name'    => Yii::t('app','Cities'),
            ];
            $links[] = [
                'url'     => '/offices',
                'name'    => Yii::t('app','Offices'),
            ];
            $links[] = [
                'url'     => '/rooms',
                'name'    => Yii::t('app','Rooms'),
            ];
        }

        return $links;
  
    }
}