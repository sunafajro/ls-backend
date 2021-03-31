<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\Auth;
use school\models\Invoicestud;
use school\models\Report;
use school\models\Student;
use school\models\User;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class InvoicesReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 * @property integer $officeId
 */
class InvoicesReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;
    /** @var integer */
    public $officeId;

    /**
     * MarginReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;

        $config['officeId'] = $auth->roleId === 4 ? $auth->officeId : ($config['officeId'] ?? null);
        list($start, $end) = DateHelper::prepareMonthlyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        $config['startDate'] = $start;
        $config['endDate'] = $end;

        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $invoices = (new Query())
            ->select('is.id as iid, sn.id as sid, sn.name as sname, u.name as uname, is.value as money, is.visible as visible, is.done as done, is.num as num, is.calc_service as id, is.data as date, is.remain as remain')
            ->from(['is' => Invoicestud::tableName()])
            ->leftJoin(['u' => User::tableName()], 'u.id = is.user')
            ->leftJoin(['sn' => Student::tableName()], 'sn.id = is.calc_studname')
            ->andFilterWhere(['is.calc_office' => $this->officeId])
            ->andFilterWhere(['>=', 'is.data', $this->startDate])
            ->andFilterWhere(['<=', 'is.data', $this->endDate])
            ->orderby(['is.data' => SORT_DESC, 'is.id' => SORT_DESC])
            ->all();

        $dates = ArrayHelper::getColumn($invoices, 'date');
        $dates = array_unique($dates);

        return [$dates, $invoices];
    }
}