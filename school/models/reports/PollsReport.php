<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use common\models\BasePoll as Poll;
use common\models\BasePollResponse as PollResponse;
use school\models\ClientAccess;
use school\models\Report;
use school\models\Student;
use yii\data\ActiveDataProvider;

/**
 * Class PollsReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 * @property integer $pollId
 */
class PollsReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;
    /** @var integer */
    public $pollId;

    /** @var Poll */
    public $poll;

    /**
     * PollsReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($start, $end) = DateHelper::prepareMonthlyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        $config['startDate'] = $start;
        $config['endDate'] = $end;
        $this->poll = Poll::find()->andFilterWhere(['id' => $config['pollId']])->orderBy(['title' => SORT_ASC])->one();
        if ($this->poll && $config['pollId'] !== $this->poll->id) {
            $config['pollId'] = $this->poll->id;
        }

        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): ActiveDataProvider
    {
        $query = (new \yii\db\Query())
            ->select(['id' => 'pr.id', 'date' => 'pr.created_at', 'studentId' => 's.id', 'studentName' => 's.name'])
            ->from([ 'pr' => PollResponse::tableName()])
            ->innerJoin(['c' => ClientAccess::tableName()], "c.calc_studname = pr.user_id")
            ->innerJoin(['s' => Student::tableName()], "s.id = c.calc_studname")
            ->andWhere([
                'pr.visible' => 1,
                'pr.poll_id' => $this->pollId,
            ])
            ->andWhere(['>=', 'pr.created_at', "{$this->startDate} 00:00:00"])
            ->andWhere(['<=', 'pr.created_at', "{$this->endDate} 23:59:59"]);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}