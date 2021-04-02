<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\Auth;
use school\models\Groupteacher;
use school\models\Invoicestud;
use school\models\Journalgroup;
use school\models\Moneystud;
use school\models\Report;
use school\models\Schedule;
use school\models\Service;
use school\models\Student;
use school\models\Teacher;
use school\models\Timenorm;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class TeacherHoursReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 * @property string $type
 * @property integer $teacherId
 * @property integer $limit
 * @property integer $offset
 */
class TeacherHoursReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;
    /** @var integer */
    public $teacherId;
    /** @var integer */
    public $limit;
    /** @var integer */
    public $offset;


    /**
     * TeacherHoursReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($start, $end) = DateHelper::prepareWeeklyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        $config['startDate'] = $start;
        $config['endDate'] = $end;

        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $teachers = (new Query())
            ->select(['id' => 't.id', 'name' => 't.name'])
            ->from(['t' => Teacher::tableName()])
            ->innerJoin(['j' => Journalgroup::tableName()], 'j.calc_teacher = t.id')
            ->where([
                't.visible' => 1,
                't.old'     => 0,
                'j.visible' => 1,
            ])
            ->andFilterWhere(['>=', 'j.data', $this->startDate])
            ->andFilterWhere(['<=', 'j.data', $this->endDate])
            ->andFilterWhere(['t.id' => $this->teacherId ?? null])
            ->groupBy(['t.id', 't.name'])
            ->orderBy(['t.name' => SORT_ASC])
            ->all();
        $teachers = ArrayHelper::map($teachers, 'id', 'name');
        $ids = array_keys($teachers);

        $lessons = (new Query())
            ->select([
                'teacherId' => 't.id',
                'teacher'   => 't.name',
                'date'      => 'j.data',
                'period'    => new Expression('CONCAT(j.time_begin, " - ", j.time_end)'),
                'hours'     => 'tn.value'
            ])
            ->from(['j' => Journalgroup::tableName()])
            ->innerJoin(['t' => Teacher::tableName()], 'j.calc_teacher = t.id')
            ->innerJoin(['g' => Groupteacher::tableName()], 'j.calc_groupteacher = g.id')
            ->innerJoin(['s' => Service::tableName()], 'g.calc_service = s.id')
            ->innerJoin(['tn' => Timenorm::tableName()], 's.calc_timenorm = tn.id')
            ->andWhere([
                'j.visible' => 1,
            ])
            ->andFilterWhere(['j.calc_teacher' => $params['tid'] ?? $ids])
            ->andFilterWhere(['>=', 'j.data', $this->startDate])
            ->andFilterWhere(['<=', 'j.data', $this->endDate])
            ->orderBy(['j.data' => SORT_ASC, 'j.time_begin' => SORT_ASC])
            ->all();

        $result = [];
        $day = \DateTime::createFromFormat('Y-m-d', $this->startDate);
        $dayString = $day->format('Y-m-d');
        while ($dayString <= $this->endDate) {
            if (!isset($result[$dayString])) {
                $result[$dayString] = [];
            }
            foreach ($lessons ?? [] as $lesson) {
                if ($lesson['date'] === $dayString) {
                    if (!isset($result[$lesson['date']][$lesson['teacherId']])) {
                        $result[$lesson['date']][$lesson['teacherId']] = [];
                    }
                    $lesson['periodHours'] = isset($lesson['period']) && $lesson['period'] ? DateHelper::strIntervalToCount($lesson['period'], ' - ', 'H:i', 'h') : null;
                    $result[$lesson['date']][$lesson['teacherId']][] = $lesson;
                }
            }
            $day->modify('+1 day');
            $dayString = $day->format('Y-m-d');
        }

        return [
            'teachers' => $teachers,
            'hours' => $result
        ];
    }
}