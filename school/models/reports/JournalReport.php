<?php

namespace school\models\reports;

use school\models\Auth;
use school\models\EducationLevel;
use school\models\Groupteacher;
use school\models\Journalgroup;
use school\models\Report;
use school\models\Service;
use school\models\Teacher;
use school\models\Timenorm;
use yii\data\Pagination;
use yii\db\Query;

/**
 * Class JournalReport
 * @package school\models\reports
 *
 *
 * @property string $corporate
 * @property string $officeId
 * @property string $teacherId
 * @property strin $page
 */
class JournalReport extends Report
{
    public $corporate = 0;
    public $officeId;
    public $teacherId;
    public $page = 1;

    /**
     * JournalsReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        /** @var Auth $auth */
        $auth = \Yii::$app->user->identity;
        if ($auth->roleId === 4) {
            $config['officeId'] = $auth->officeId;
            if ($config['corporate']) {
                $config['officeId'] = NULL;
            }
        }
        if (isset($config['page']) && $config['page'] < 0) {
            unset($config['page']);
        }
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $teachersQuery = (new Query())
            ->select('t.id as tid, t.name as tname')
            ->distinct()
            ->from('calc_teachergroup tg')
            ->innerJoin(['t' => Teacher::tableName()], 't.id=tg.calc_teacher')
            ->innerJoin(['gt' => Groupteacher::tableName()], 'gt.id=tg.calc_groupteacher')
            ->where([
                'gt.visible' => 1,
                'tg.visible' => 1
            ])
            ->andFilterWhere(['t.id' => $this->teacherId ?? NULL])
            ->andFilterWhere(['gt.calc_office' => $this->officeId ?? NULL])
            ->andFilterWhere(['gt.corp' => $this->corporate ?? NULL]);

        $teachersCountQuery = clone $teachersQuery;
        $pages = new Pagination(['totalCount' => $teachersCountQuery->count()]);
        $limit = 10;
        $offset = 0;
        if ($this->page) {
            if ($this->page > 1 && $this->page <= $pages->totalCount) {
                $offset = 10 * ($this->page - 1);
            }
        }
        $teachers = $teachersQuery->orderBy(['t.name' => SORT_ASC])->limit($limit)->offset($offset)->all();
        $teachersAll = $teachersCountQuery->orderBy(['t.name' => SORT_ASC])->all();

        $teacherIds = NULL;
        $teacherNames = NULL;
        $teacherLessonsCount = [];
        if (!$this->teacherId) {
            foreach ($teachers as $i => $teacher) {
                $teacherIds[$i] = $teacher['tid'];
                $teacherNames[$teacher['tid']] = $teacher['tname'];
                $teacherLessonsCount[$teacher['tid']]['totalCount'] = 0;
            }
        } else {
            foreach ($teachersAll as $teacher) {
                if ($teacher['tid'] == $this->teacherId) {
                    $teacherNames[$teacher['tid']] = $teacher['tname'];
                    $teacherLessonsCount[$teacher['tid']]['totalCount'] = 0;
                }
            }
        }

        $lessons = [];
        $groups = [];

        if (!empty($teacherNames)) {
            $lessons = (new Query())
                ->select('jg.id as lid, jg.type as type, jg.calc_groupteacher as gid, jg.data as date, jg.done as done, jg.calc_teacher as tid, t.name as tname, jg.description as desc, jg.visible as visible')
                ->from(['jg' => Journalgroup::tableName()])
                ->leftJoin(['t' => Teacher::tableName()], 't.id=jg.calc_teacher')
                ->leftJoin(['gt' => Groupteacher::tableName()], 'gt.id=jg.calc_groupteacher')
                ->where([
                    'jg.view' => 0,
                    'jg.visible' => 1
                ])
                ->andWhere(['>', 'jg.user', 0])
                ->andFilterWhere(['gt.calc_office' => $this->officeId ?? NULL])
                ->andFilterWhere(['jg.calc_teacher' => $this->teacherId ?? NULL])
                ->andFilterWhere(['in', 'jg.calc_teacher', $teacherIds])
                ->andFilterWhere(['gt.corp' => $this->corporate ?? NULL])
                ->orderby(['t.name' => SORT_ASC, 'jg.data' => SORT_DESC])
                ->all();

            $groups = (new Query())
                ->select('tg.calc_groupteacher as gid, tg.calc_teacher as tid, s.id as sid, s.name as service, el.name as ename, tn.value as hours')
                ->from('calc_teachergroup tg')
                ->leftJoin(['gt' => Groupteacher::tableName()], 'gt.id=tg.calc_groupteacher')
                ->leftJoin(['s' => Service::tableName()], 's.id=gt.calc_service')
                ->leftJoin(['tn' => Timenorm::tableName()], 'tn.id=s.calc_timenorm')
                ->leftJoin(['el' => EducationLevel::tableName()], 'el.id=gt.calc_edulevel')
                ->where(['gt.visible' => 1])
                ->andFilterWhere(['gt.calc_office' => $this->officeId ?? NULL])
                ->andFilterWhere(['tg.calc_teacher' => $this->teacherId ?? NULL])
                ->andFilterWhere(['in', 'tg.calc_teacher', $teacherIds])
                ->andFilterWhere(['gt.corp' => $this->corporate ?? NULL])
                ->orderby(['tg.id' => SORT_ASC])
                ->all();

            foreach ($groups as $group) {
                $total = 0;
                foreach ($lessons as $lesson) {
                    if ($lesson['tid'] == $group['tid'] && $lesson['gid'] == $group['gid']) {
                        $total++;
                    }
                }
                $teacherLessonsCount[$group['tid']][$group['gid']]['totalCount'] = $total;
                $teacherLessonsCount[$group['tid']]['totalCount'] += $total;
            }
        }

        return [$groups, $lessons, $teacherNames, $teacherLessonsCount, $pages];
    }
}