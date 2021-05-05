<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\ClientAccess;
use school\models\LoginLog;
use school\models\Report;
use school\models\Student;
use client\Client;
use yii\data\ActiveDataProvider;

/**
 * Class LoginsReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 */
class LoginsReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;

    /**
     * LoginsReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($start, $end) = DateHelper::prepareMonthlyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        $config['startDate'] = $start;
        $config['endDate'] = $end;

        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): ActiveDataProvider
    {
        $query = (new \yii\db\Query())
            ->select(['id' => 'l.user_id', 'name' => 's.name', 'count' => 'COUNT(*)'])
            ->from([ 'l' => LoginLog::tableName()])
            ->innerJoin(['c' => ClientAccess::tableName()], "c.calc_studname = l.user_id")
            ->innerJoin(['s' => Student::tableName()], "s.id = c.calc_studname")
            ->andWhere([
                'l.result' => LoginLog::ACTION_LOGIN,
                'l.module_type' => Client::MODULE_NAME,
            ])
            ->andWhere(['>=', 'l.date', "{$this->startDate} 00:00:00"])
            ->andWhere(['<=', 'l.date', "{$this->endDate} 23:59:59"])
            ->groupBy(['l.user_id', 's.name']);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}