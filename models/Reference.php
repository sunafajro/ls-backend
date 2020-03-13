<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Модель меню ссылок раздела Справочники
 */
class Reference extends Model
{
    const TYPE_CITIES        = 'cities';
    const TYPE_COEFFICIENTS  = 'coefficients';
    const TYPE_CONTACTS      = 'contacts';
    const TYPE_LANGUAGES     = 'languages';
    const TYPE_OFFICES       = 'offices';
    const TYPE_PREMIUMS      = 'premiums';
    const TYPE_ROOMS         = 'rooms';
    const TYPE_STUDENT_NORMS = 'studentnorms';
    const TYPE_TEACHER_NORMS = 'teachernorms';
    const TYPE_TIME_NORMS    = 'timenorms';
    const TYPE_VOLONTEERS    = 'volonteers';

   public static function getReferenceTypes()
   {
        $types = [
            self::TYPE_CONTACTS      => Yii::t('app','Phones'),
            self::TYPE_VOLONTEERS    => Yii::t('app','Volunteers'),
        ];
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $types = array_merge($types, [
                self::TYPE_LANGUAGES     => Yii::t('app','Languages'),
                self::TYPE_STUDENT_NORMS => Yii::t('app','Student norms'),
                self::TYPE_TEACHER_NORMS => Yii::t('app','Teacher norms'),
                self::TYPE_TIME_NORMS    => Yii::t('app','Time norms'),
                self::TYPE_PREMIUMS      => Yii::t('app','Language premiums'),
                self::TYPE_COEFFICIENTS  => Yii::t('app','Accrual coefficients'),
                self::TYPE_CITIES        => Yii::t('app','Cities'),
                self::TYPE_OFFICES       => Yii::t('app','Offices'),
                self::TYPE_ROOMS         => Yii::t('app','Rooms'),
            ]);
        }

        return $types;
   }

    public static function getLinks()
    {
        $links = [];
        foreach (self::getReferenceTypes() as $key => $value) {
            $links[] = [
                'url'  => "/{$key}",
                'name' => $value,
            ];
        }

        return $links; 
    }
}