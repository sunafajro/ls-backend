<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use common\models\BasePoll as Poll;
use common\models\BasePollResponse as PollResponse;
use common\models\BasePollQuestionResponse as PollQuestionResponse;
use school\models\ClientAccess;
use school\models\Report;
use school\models\Student;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

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

    /**
     * @return array
     */
    public function prepareTotals(): array
    {
        $result = [];
        $responseQuery = (new \yii\db\Query())
            ->select(['id' => 'pr.id', 'questionId' => 'pqr.poll_question_id', 'items' => 'pqr.items'])
            ->from([ 'pr' => PollResponse::tableName()])
            ->innerJoin(['pqr' => PollQuestionResponse::tableName()], "pqr.poll_response_id = pr.id AND pqr.visible = 1")
            ->andWhere([
                'pr.visible' => 1,
                'pr.poll_id' => $this->pollId,
            ])
            ->andWhere(['>=', 'pr.created_at', "{$this->startDate} 00:00:00"])
            ->andWhere(['<=', 'pr.created_at', "{$this->endDate} 23:59:59"]);;
        foreach ($responseQuery->each() as $response) {
            if (!isset($result[$response['questionId']])) {
                $result[$response['questionId']] = [];
            }
            $responses = JSON::decode($response['items']);
            foreach($responses as $key => $item) {
                if (!isset($result[$response['questionId']][$key])) {
                    $result[$response['questionId']][$key] = [
                        'count' => 0,
                    ];
                }
                $result[$response['questionId']][$key]['count'] += $item['value'] ?? 0;
                foreach ($item['options'] ?? [] as $subKey => $subItem) {
                    if (!isset($result[$response['questionId']][$key]['options'])) {
                        $result[$response['questionId']][$key]['options'] = [];
                    }
                    if (!isset($result[$response['questionId']][$key]['options'][$subKey])) {
                        $result[$response['questionId']][$key]['options'][$subKey] = 0;
                    }
                    $result[$response['questionId']][$key]['options'][$subKey] += $subItem['value'] ?? 0;
                }
            }
        }

        return $result;
    }
}