<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\Auth;
use school\models\Moneystud;
use school\models\Notification;
use school\models\Office;
use school\models\Report;
use school\models\Student;
use school\models\User;

/**
 * Class PaymentsReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 * @property integer $officeId
 */
class PaymentsReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;
    /** @var integer */
    public $officeId;

    /**
     * PaymentsReport constructor.
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
        $result = [];
        $payments = (new \yii\db\Query())
            ->select([
                'id'                 => 'ms.id',
                'studentId'          => 'sn.id',
                'student'            => 'sn.name',
                'manager'            => 'u.name',
                'sum'                => 'ms.value',
                'card'               => 'ms.value_card',
                'cash'               => 'ms.value_cash',
                'bank'               => 'ms.value_bank',
                'date'               => 'ms.data',
                'receipt'            => 'ms.receipt',
                'active'             => 'ms.visible',
                'remain'             => 'ms.remain',
                'officeId'           => 'ms.calc_office',
                'office'             => 'o.name',
                'notificationId'     => 'n.id',
                'notification'       => 'n.status',
            ])
            ->from(['ms' => Moneystud::tableName()])
            ->innerjoin(['sn' => Student::tableName()], 'sn.id = ms.calc_studname')
            ->innerJoin(['u' => User::tableName()], 'u.id = ms.user')
            ->innerJoin(['o' => Office::tableName()], 'o.id = ms.calc_office')
            ->leftJoin(['n' => Notification::tableName()], 'n.entity_id = ms.id')
            ->andFilterWhere(['ms.calc_office' => $this->officeId])
            ->andFilterWhere(['>=', 'ms.data', $this->startDate])
            ->andFilterWhere(['<=', 'ms.data', $this->endDate])
            ->orderby(['ms.data'=>SORT_DESC, 'ms.id'=>SORT_DESC])
            ->all();

        foreach($payments as $p) {
            if (!isset($result[$p['officeId']])) {
                $result[$p['officeId']] = [
                    'name' => $p['office'],
                    'counts' => [
                        Moneystud::PAYMENT_TYPE_CASH => 0,
                        Moneystud::PAYMENT_TYPE_CARD => 0,
                        Moneystud::PAYMENT_TYPE_BANK => 0,
                        'all' => 0
                    ]
                ];
            }
            if (!isset($result[$p['officeId']][$p['date']])) {
                $result[$p['officeId']][$p['date']] = [
                    'counts' => [
                        Moneystud::PAYMENT_TYPE_CASH => 0,
                        Moneystud::PAYMENT_TYPE_CARD => 0,
                        Moneystud::PAYMENT_TYPE_BANK => 0,
                        'all' => 0
                    ],
                    'rows' => []
                ];
            }
            $type = '';
            if ($p['card'] != '0.00') {
                $type = Moneystud::PAYMENT_TYPE_CARD;
                if ($p['active'] && !$p['remain']) {
                    $result[$p['officeId']]['counts'][$type] += $p[$type];
                    $result[$p['officeId']][$p['date']]['counts'][$type] += $p[$type];
                }
            }
            if ($p['cash'] != '0.00') {
                $type = Moneystud::PAYMENT_TYPE_CASH;
                if ($p['active'] && !$p['remain']) {
                    $result[$p['officeId']]['counts'][$type] += $p[$type];
                    $result[$p['officeId']][$p['date']]['counts'][$type] += $p[$type];
                }
            }
            if ($p['bank'] != '0.00') {
                $type = Moneystud::PAYMENT_TYPE_BANK;
                if ($p['active'] && !$p['remain']) {
                    $result[$p['officeId']]['counts'][$type] += $p[$type];
                    $result[$p['officeId']][$p['date']]['counts'][$type] += $p[$type];
                }
            }
            if ($p['active'] && !$p['remain']) {
                $result[$p['officeId']]['counts']['all'] += $p['sum'];
                $result[$p['officeId']][$p['date']]['counts']['all'] += $p['sum'];
            }
            $result[$p['officeId']][$p['date']]['rows'][] = [
                'id'                 => $p['id'],
                'studentId'          => $p['studentId'],
                'student'            => $p['student'],
                'manager'            => $p['manager'],
                'receipt'            => $p['receipt'],
                'type'               => $type,
                'sum'                => $p['sum'],
                'active'             => $p['active'],
                'remain'             => $p['remain'],
                'notificationId'     => $p['notificationId'],
                'notification'       => $p['notification'],
            ];
        }

        return $result;
    }
}