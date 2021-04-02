<?php

namespace school\models\reports;

use school\models\Auth;
use school\models\Groupteacher;
use school\models\Invoicestud;
use school\models\Journalgroup;
use school\models\Moneystud;
use school\models\Report;
use school\models\Schedule;
use school\models\Service;
use school\models\Student;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class DebtsReport
 * @package school\models\reports
 *
 * @property string $name
 * @property string $state
 * @property string $type
 * @property integer $officeId
 * @property integer $page
 */
class DebtsReport extends Report
{
    /** @var string */
    public $name;
    /** @var string */
    public $state;
    /** @var string */
    public $type;
    /** @var integer */
    public $officeId;
    /** @var integer */
    public $page;

    /**
     * DebtsReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        $config['officeId'] = $auth->roleId === 4 ? $auth->officeId : ($config['officeId'] ?? null);
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $studentList = (new Query())
            ->select(['id' => 'sn.id', 'name' => 'sn.name', 'debt' => 'sn.debt'])
            ->from(['sn' => Student::tableName()]);
        if ($this->officeId) {
            $studentList->innerJoin(['so' => 'student_office'], 'sn.id = so.student_id');
        }
        $studentList
            ->where([
                'sn.visible' => 1
            ])
            ->andFilterWhere([
                $this->type === '1' ? '>' : '<',
                'sn.debt',
                $this->type === '' ? NULL: 0
            ])
            ->andFilterWhere(['sn.active'    => $this->state === '' ? NULL : $this->state])
            ->andFilterWhere(['so.office_id' => $this->officeId === '' ? NULL : $this->officeId])
            ->andFilterWhere(['like', 'sn.name', $this->name]);

        $countQuery = clone $studentList;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $limit = 20;
        $offset = 0;
        if ($this->page > 1 && ($this->page <= $pages->totalCount)) {
            $offset = 20 * ($this->page - 1);
        }

        $studentList = $studentList->orderby(['sn.name'=>SORT_ASC])->limit($limit)->offset($offset)->all();
        $studentIds = ArrayHelper::getColumn($studentList, 'id');
        $studentIds = array_unique($studentIds);

        $students = [];
        if (!empty($studentIds)) {
            $students = (new Query())
                ->select('s.id as sid, s.name as sname, is.calc_studname as stid, SUM(is.num) as num')
                ->distinct()
                ->from(['s' => Service::tableName()])
                ->leftjoin(['is' => Invoicestud::tableName()], 'is.calc_service = s.id')
                ->where([
                    'is.remain' => [Invoicestud::TYPE_NORMAL, Invoicestud::TYPE_NETTING],
                    'is.visible' => 1
                ])
                ->andWhere(['in', 'is.calc_studname', $studentIds])
                ->groupby(['is.calc_studname', 's.id'])
                ->orderby(['s.id' => SORT_ASC])
                ->all();

            if (!empty($students)) {
                foreach($students as $i => $service){
                    $schedule = new Schedule();
                    $studentSchedule = $schedule->getStudentSchedule($service['stid']);
                    $lessons = (new Query())
                        ->select('COUNT(sjg.id) AS cnt')
                        ->from('calc_studjournalgroup sjg')
                        ->leftjoin(['gt' => Groupteacher::tableName()], 'sjg.calc_groupteacher=gt.id')
                        ->leftjoin(['jg' => Journalgroup::tableName()], 'sjg.calc_journalgroup=jg.id')
                        ->where(['jg.view' => 1, 'jg.visible' => 1, 'gt.calc_service' => $service['sid'], 'sjg.calc_studname' => $service['stid']])
                        ->andWhere(['in', 'sjg.calc_statusjournal', [Journalgroup::STUDENT_STATUS_PRESENT, Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED]])
                        ->one();
                    $count = $students[$i]['num'] - $lessons['cnt'];
                    $students[$i]['num'] = $count;
                    $students[$i]['npd'] = Moneystud::getNextPaymentDay($studentSchedule, $service['sid'], $count);
                }
            }
        }

        return [$studentList, $students, $pages];
    }
}