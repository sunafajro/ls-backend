<?php

namespace school\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
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
        $items = [];
        if (AccessRule::checkAccess('report_common')) {
            $items[] = [
                'id'    => 'common',
                'label' => Yii::t('app','Common'),
                'url'   => Url::to(['report/common']),
            ];
        }
        if (AccessRule::checkAccess('report_margin')) {
            $items[] = [
                'id' => 'margin',
                'label' => Yii::t('app','Margin'),
                'url' => Url::to(['report/margin']),
            ];
        }
        if (AccessRule::checkAccess('report_payments')) {
            $items[] = [
                'id' => 'payments',
                'label' => Yii::t('app','Payments'),
                'url' => Url::to(['report/payments']),
            ];
        }
        if (AccessRule::checkAccess('report_invoices')) {
            $items[] = [
                'id' => 'invoices',
                'label' => Yii::t('app','Invoices'),
                'url' => Url::to(['report/invoices']),
            ];
        }
        if (AccessRule::checkAccess('report_sales')) {
            $items[] = [
                'id' => 'sales',
                'label' => Yii::t('app','Sales'),
                'url' => Url::to(['report/sale']),
            ];
        }
        if (AccessRule::checkAccess('report_debt')) {
            $items[] = [
                'id' => 'debts',
                'label' => Yii::t('app','Debts'),
                'url' => Url::to(['report/debt']),
            ];
        }
        if (AccessRule::checkAccess('report_journals')) {
            $items[] = [
                'id' => 'journals',
                'label' => Yii::t('app','Journals'),
                'url' => Url::to(['report/journals']),
            ];
        }
        if (AccessRule::checkAccess('report_accruals')) {
            $items[] = [
                'id' => 'accruals',
                'label' => Yii::t('app','Accruals'),
                'url' => Url::to(['report/accrual']),
            ];
        }
        if (AccessRule::checkAccess('report_salaries')) {
            $items[] = [
                'id' => 'salaries',
                'label' => Yii::t('app','Salaries'),
                'url' => Url::to(['report/salaries']),
            ];
        }
        if (AccessRule::checkAccess('report_office-plan')) {
            $items[] = [
                'id' => 'office-plan',
                'label' => Yii::t('app','Office plan'),
                'url' => Url::to(['report/office-plan']),
            ];
        }
        if (AccessRule::checkAccess('report_lessons')) {
            $items[] = [
                'id' => 'lessons',
                'label' => Yii::t('app','Lessons'),
                'url' => Url::to(['report/lessons']),
            ];
        }
        if (AccessRule::checkAccess('report_teacher-hours')) {
            $items[] = [
                'id' => 'teacher-hours',
                'label' => Yii::t('app','Teacher hours'),
                'url' => Url::to(['report/teacher-hours']),
            ];
        }
        if (AccessRule::checkAccess('report_commissions')) {
            $items[] = [
                'id' => 'commissions',
                'label' => Yii::t('app','Commissions'),
                'url' => Url::to(['report/commissions']),
            ];
        }

        if (AccessRule::checkAccess('report_logins')) {
            $items[] = [
                'id' => 'logins',
                'label' => Yii::t('app','Logins'),
                'url' => Url::to(['report/logins']),
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
     * @return array|ActiveDataProvider
     */
    public function prepareReportData()
    {
        return [];
    }
}
