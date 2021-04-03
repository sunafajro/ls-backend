<?php

namespace school\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class Report
 * @package school\models
 */
class Report extends Model
{
    /**
     * @return array
     */
    public static function getReportTypes() : array
    {
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        $roleId = $auth->roleId;
        $items = [];
        if (in_array($roleId, [3, 8])) {
            $items[] = [
                'id'    => 'common',
                'label' => Yii::t('app','Common'),
                'url'   => Url::to(['report/common']),
            ];
        }
        if (in_array($roleId, [3])) {
            $items[] = [
                'id' => 'margin',
                'label' => Yii::t('app','Margin'),
                'url' => Url::to(['report/margin']),
            ];
        }
        if (in_array($roleId, [3, 4, 8])) {
            $items[] = [
                'id' => 'payments',
                'label' => Yii::t('app','Payments'),
                'url' => Url::to(['report/payments']),
            ];
        }
        if (in_array($roleId, [3, 4])) {
            $items[] = [
                'id' => 'invoices',
                'label' => Yii::t('app','Invoices'),
                'url' => Url::to(['report/invoices']),
            ];
        }
        if (in_array($roleId, [3])) {
            $items[] = [
                'id' => 'sales',
                'label' => Yii::t('app','Sales'),
                'url' => Url::to(['report/sale']),
            ];
        }
        if (in_array($roleId, [3, 4])) {
            $items[] = [
                'id' => 'debts',
                'label' => Yii::t('app','Debts'),
                'url' => Url::to(['report/debt']),
            ];
        }
        if (in_array($roleId, [3, 4])) {
            $items[] = [
                'id' => 'journals',
                'label' => Yii::t('app','Journals'),
                'url' => Url::to(['report/journals']),
            ];
        }
        if (in_array($roleId, [3, 8])) {
            $items[] = [
                'id' => 'accruals',
                'label' => Yii::t('app','Accruals'),
                'url' => Url::to(['report/accrual']),
            ];
        }
        if (in_array($roleId, [3, 8])) {
            $items[] = [
                'id' => 'salaries',
                'label' => Yii::t('app','Salaries'),
                'url' => Url::to(['report/salaries']),
            ];
        }
        if (in_array($roleId, [3])) {
            $items[] = [
                'id' => 'office-plan',
                'label' => Yii::t('app','Office plan'),
                'url' => Url::to(['report/office-plan']),
            ];
        }
        if (in_array($roleId, [3, 4, 6])) {
            $items[] = [
                'id' => 'lessons',
                'label' => Yii::t('app','Lessons'),
                'url' => Url::to(['report/lessons']),
            ];
        }
        if (in_array($roleId, [3, 4, 6])) {
            $items[] = [
                'id' => 'teacher-hours',
                'label' => Yii::t('app','Teacher hours'),
                'url' => Url::to(['report/teacher-hours']),
            ];
        }
        if (in_array($roleId, [3, 4, 8])) {
            $items[] = [
                'id' => 'commissions',
                'label' => Yii::t('app','Commissions'),
                'url' => Url::to(['report/commissions']),
            ];
        }

        return $items;
    }

    /**
     * Список отчетов для создания выпадающего меню
     * 
     * @return array
     */
    public static function getReportTypeList() : array
    {
        return ArrayHelper::map(static::getReportTypes(), 'label', 'url');
    }

    /**
     * @return array
     */
    public function prepareReportData(): array
    {
        return [];
    }
}
