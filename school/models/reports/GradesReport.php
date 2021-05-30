<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\Report;
use school\models\searches\StudentGradeSearch;
use yii\data\ActiveDataProvider;

/**
 * Class GradesReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 * @property integer $pollId
 */
class GradesReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;

    /**
     * PollsReport constructor.
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
     * @throws \yii\base\InvalidConfigException
     */
    public function prepareReportData(array $params = []): ActiveDataProvider
    {
        $searchModel = $this->getSearchModel();
        $formName = $searchModel->formName();
        if (!isset($params[$formName])) {
            $params[$formName] = [];
        }
        if ($this->startDate) {
            $params[$formName]['startDate'] = $this->startDate;
        }
        if ($this->endDate) {
            $params[$formName]['endDate'] = $this->endDate;
        }
        return $searchModel->search($params);
    }

    /**
     * @return StudentGradeSearch
     */
    public function getSearchModel(): StudentGradeSearch
    {
        return new StudentGradeSearch();
    }
}