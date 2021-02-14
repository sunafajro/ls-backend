<?php
namespace client\models;

use Yii;

use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Html;

/**
 * This is the model class for table "calc_studname".
 *
 * @property integer $id
 * @property string $name
 * @property string $fname
 * @property string $lname
 * @property string $mname
 * @property string $email
 * @property integer $visible
 * @property integer $history
 * @property string $phone
 * @property string $address 
 * @property float debt
 * @property float debt2
 * @property float invoice
 * @property float money
 * @property integer calc_sex
 * @property integer calc_cumulativediscount
 * @property integer active
 * @property integer calc_way
 * @property string description
 */
class Student extends ActiveRecord
{
    const EXAM_YLE_STARTERS    = 'yleStarters';
    const EXAM_YLE_MOVERS      = 'yleMovers';
    const EXAM_YLE_FLYERS      = 'yleFlyers';
    const EXAM_KET_A2          = 'ketA2';
    const EXAM_PET_B1          = 'petB1';
    const EXAM_FCE_B2          = 'fceB2';
    const EXAM_TEXT_BOOK_FINAL = 'text_book_final';
    const EXAM_OLYMPIAD        = 'olympiad';
    const EXAM_DICTATION       = 'dictation';

    const EXAM_CONTENT_LISTENING           = 'listening';
    const EXAM_CONTENT_READING_AND_WRITING = 'readingAndWriting';
    const EXAM_CONTENT_SPEAKING            = 'speaking';
    const EXAM_CONTENT_READING             = 'reading';
    const EXAM_CONTENT_USE_OF_ENGLISH      = 'useOfEnglish';
    const EXAM_CONTENT_WRITING             = 'writing';

    const EXAM_CONTENT_WROTE_AN           = 'wroteAn';
    const EXAM_CONTENT_TOOK_PART_IN       = 'tookPartIn';
    const EXAM_CONTENT_BECAME_WHO         = 'becameWho';
    const EXAM_CONTENT_TOOK_THE_COURSE    = 'tookTheCourse';
    const EXAM_CONTENT_ACCORDING_TO_BOOK  = 'according_to_book';
    const EXAM_CONTENT_COURSE_HOURS_COUNT = 'course_hours_count';

    // статусы посещения занятия
    const STUDENT_STATUS_PRESENT         = 1;
    const STUDENT_STATUS_ABSENT_WARNED   = 2;
    const STUDENT_STATUS_ABSENT_UNWARNED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'calc_studname';
    }

    /**
     * @return string[]
     */
    public static function getExams() : array
    {
        return [
            self::EXAM_YLE_STARTERS    => 'YLE starters',
            self::EXAM_YLE_MOVERS      => 'YLE movers',
            self::EXAM_YLE_FLYERS      => 'YLE flyers',
            self::EXAM_KET_A2          => 'KET - A2',
            self::EXAM_PET_B1          => 'PET - B1',
            self::EXAM_FCE_B2          => 'FCE - B2',
            self::EXAM_TEXT_BOOK_FINAL => 'Итоговый тест по учебнику',
            self::EXAM_OLYMPIAD        => 'Олимпиада',
            self::EXAM_DICTATION       => 'Тотальный диктант',
        ];
    }

    /**
     * @return string[]
     */
    public static function getExamContentTypes() : array
    {
        return [
            self::EXAM_CONTENT_LISTENING            => 'Listening',
            self::EXAM_CONTENT_READING_AND_WRITING  => 'Reading & Writing',
            self::EXAM_CONTENT_SPEAKING             => 'Speaking',
            self::EXAM_CONTENT_READING              => 'Reading',
            self::EXAM_CONTENT_USE_OF_ENGLISH       => 'Use of English',
            self::EXAM_CONTENT_WRITING              => 'Writing',
            self::EXAM_CONTENT_WROTE_AN             => 'Написал',
            self::EXAM_CONTENT_TOOK_PART_IN         => 'Принял участие в',
            self::EXAM_CONTENT_BECAME_WHO           => 'Стал',
            self::EXAM_CONTENT_TOOK_THE_COURSE      => 'По прохождению курса',
            self::EXAM_CONTENT_ACCORDING_TO_BOOK    => 'По учебнику',
            self::EXAM_CONTENT_COURSE_HOURS_COUNT   => 'В количестве часов',
        ];
    }

    /**
     * @return string[]
     */
    public static function getAttendanceStatuses(): array
    {
        return [
            self::STUDENT_STATUS_PRESENT         => 'присутствовал',
            // (предупредил)
            self::STUDENT_STATUS_ABSENT_WARNED   => 'не было (принес справку)',
            // (не предупредил)
            self::STUDENT_STATUS_ABSENT_UNWARNED => 'не было',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'visible', 'history', 'calc_sex','active'], 'required'],
            [['name', 'fname', 'lname', 'mname', 'email', 'phone', 'address', 'description'], 'string'],
            [['visible', 'history', 'calc_sex', 'calc_cumulativediscount', 'active', 'calc_way'], 'integer'],
            [['debt', 'debt2', 'invoice', 'money'], 'number']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Full name'),
            'fname' => Yii::t('app', 'First name'),
            'lname' => Yii::t('app', 'Last name'),
            'mname' => Yii::t('app', 'Middle name'),
            'email' => Yii::t('app', 'Email'),
            'address' => Yii::t('app', 'Address'),
            'visible' => Yii::t('app', 'Visible'),
            'history' => Yii::t('app', 'History'),
            'phone' => Yii::t('app', 'Phone'),
            'debt' => Yii::t('app', 'Debt'),
            'debt2' => Yii::t('app', 'Debt'),
            'invoice' => Yii::t('app','Summary invoices'),
            'money' => Yii::t('app', 'Summary payments'),
            'calc_sex' => Yii::t('app', 'Sex'),
            'calc_cumulativediscount' => Yii::t('app','Cumulative discount'),
            'active' => Yii::t('app','Active'),
            'calc_way' => Yii::t('app','Way to Attract'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    public function findByIdOrUsername(string $id = null, string $username = null)
    {
        if (!$id && !$username) {
            return null;
        }

        $student = (new Query())
        ->select([
            'id'       => 's.id',
            'active'   => 's.active',
            'name'     => 's.name',
            'username' => 'c.username',
            'password' => 'c.password',
            'date'     => 'c.date',
        ])
        ->from(['s' => static::tableName()])
        ->innerJoin(['c' => 'tbl_client_access'], 'c.calc_studname = s.id')
        ->where([
            's.active'  => 1,
            's.visible' => 1,
            'c.site'    => 1,
        ])
        ->andFilterWhere(['c.id' => $id])
        ->andFilterWhere(['c.username' => $username])
        ->one();

        return $student ? [
            'id' => $student['id'],
            'name' => $student['name'],
            'username' => $student['username'],
            'password' => $student['password'],
            'authKey' => '',
            'accessToken' => '',
            'isActive' => $student['active'],
            'lastLoginDate' => $student['date']
        ] : null;
    }

    public function getNews()
    {
        $query = (new Query())
        ->select([
            'date' => 'm.data',
            'files' => 'm.files',
            'id' => 'm.id',
            'text' => 'm.description',
            'title' => 'm.name',
        ])
        ->from(['m' => 'calc_message'])
        ->where([
            'm.calc_messwhomtype' => '12',
            'm.send' => 1,
            'm.visible' => 1,
        ])
        ->orderBy(['m.data' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
    }

    public function getLessonsComments($limit = 10, $offset = 0)
    {
        $comments = (new Query())
		->select([
            'id'        => 'jg.id',
            'date'      => 'jg.data',
            'comments'  => 'sjg.comments',
            'successes' => 'sjg.successes',
        ])
		->from(['sjg' => 'calc_studjournalgroup'])
		->innerJoin(['jg' => 'calc_journalgroup'], 'jg.id = sjg.calc_journalgroup')
		->where([
            'sjg.calc_studname' => $this->id,
            'jg.visible' => 1
        ])
        ->andWhere(['!=', 'sjg.comments', '']);

        $countQuery = clone $comments;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

		$comments = $comments->orderBy(['jg.data' => SORT_DESC])
        ->limit($limit)
        ->offset($offset)
        ->all();
        return [
            $comments,
            $pages,
        ];
    }

    public function getPayments()
    {
        $payments = (new Query())
		->select([
            'date' => 'ms.data',
            'value' => 'ms.value',
            'office' => 'o.name',
            'employee' => 'u.name'
        ])
		->from(['ms' => 'calc_moneystud'])
		->innerJoin(['u' => 'users'], 'u.id = ms.user')
		->innerJoin(['o' => 'calc_office'], 'o.id = ms.calc_office')
		->where([
            'ms.calc_studname' => $this->id,
            'ms.visible' => 1
        ])
		->orderBy(['ms.data' => SORT_DESC])
        ->all();
        
        return $payments;
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function getCourses(int $id = null): array
    {
        return (new Query())
            ->select([
                'id' => 's.id',
                'title' => 's.name',
                'timeNorm' => 'tn.value',
                'groups' => new Expression("GROUP_CONCAT(g.id)"),
                'offices' => new Expression("GROUP_CONCAT(DISTINCT o.name SEPARATOR ', ')"),
                'startDate' => new Expression("MIN(g.data)"),
                'endDates' => new Expression("GROUP_CONCAT(g.data_visible)"),
                'levels' => new Expression("GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ')"),
                'lessonsCount' => new Expression("COUNT(j.id)"),
            ])
            ->from(['s' => 'calc_service'])
            ->innerJoin(['g' => 'calc_groupteacher'], "g.calc_service = s.id")
            ->innerJoin(['sg' => 'calc_studgroup'], "sg.calc_groupteacher = g.id")
            ->innerJoin(['tn' => 'calc_timenorm'], 'tn.id = s.calc_timenorm')
            ->innerJoin(['j' => 'calc_journalgroup'], 'j.calc_groupteacher = g.id')
            ->innerJoin(['o' => 'calc_office'], 'o.id = g.calc_office')
            ->innerJoin(['l' => 'calc_edulevel'], 'l.id = g.calc_edulevel')
            ->innerJoin(
                ['sj' => 'calc_studjournalgroup'],
                'sj.calc_journalgroup = j.id AND j.visible = :visible AND sj.calc_statusjournal != :status',
                [':visible' => 1, ':status' => 2]
            )
            ->where([
                'sg.calc_studname' => $this->id,
                'sj.calc_studname' => $this->id,
            ])
            ->andFilterWhere(['s.id' => $id])
            ->groupBy(['s.id'])
            ->orderBy(['g.data' => SORT_ASC, 's.id' => SORT_DESC, 'g.id' => SORT_DESC])
            ->cache(10 * 60)
            ->all();
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function getGroupsByCourseId(int $id): array
    {
        return (new Query())
            ->select('*')
            ->from('calc_groupteacher')
            ->where(['calc_service' => $id])
            ->all();
    }

    public function getAttestation($id)
    {
        $attestation = (new Query())
        ->select([
            'id' => 'sg.id',
            'date' => 'sg.date',
            'score' => 'sg.score',
            'type' => 'sg.type',
            'description' => 'sg.description',
            'contents' => 'sg.contents',
        ])
        ->from(['sg' => 'student_grades'])
        ->where([
            'sg.id' => $id,
            'sg.calc_studname' => $this->id,
            'sg.visible' => 1,
        ])
        ->one();

        return $attestation;
    }

    public function getAttestations()
    {
        $query = (new Query())
        ->select([
            'id'          => 'sg.id',
            'date'        => 'sg.date',
            'score'       => 'sg.score',
            'type'        => 'sg.type',
            'description' => 'sg.description',
            'contents'    => 'sg.contents',
        ])
        ->from(['sg' => 'student_grades'])
        ->where([
            'sg.calc_studname' => $this->id,
            'sg.visible'       => 1,
        ])
        ->orderBy(['sg.date' => SORT_DESC]);
        
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'date',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ],
            ],
        ]);
    }

    public function getPassedLessonsByService()
    {
        $lessons = (new Query())
		->select([
            'lessonAttend' => 'count(sjg.id)',
            'serviceId' => 'gt.calc_service',
        ])
		->from(['sjg' => 'calc_studjournalgroup'])
		->innerJoin(['jg' => 'calc_journalgroup'], 'jg.id = sjg.calc_journalgroup')
		->innerJoin(['gt' => 'calc_groupteacher'], 'gt.id = jg.calc_groupteacher and gt.id = sjg.calc_groupteacher')
		->where([
            'sjg.calc_studname' => $this->id,
            'jg.visible' => 1,
            'sjg.calc_statusjournal' => [1, 3],
            'jg.view' => 1,
        ])
        ->groupBy(['serviceid'])
        ->all();
        
        return $lessons;
    }

    public function getOrderedLessonsByService($ids = [])
    {
        $languages = [];
        $mdLanguages = (new Query())
        ->select(['id' => 'id', 'name' => 'name'])
        ->from(['l' => 'calc_lang'])
        ->where([
            'visible' => 1
        ])
        ->all();

        foreach($mdLanguages as $language) {
            $languages[$language['id']] = $language['name'];
        }

        $eduforms = [];
        $mdEduforms = (new Query())
        ->select(['id' => 'id', 'name' => 'name'])
        ->from(['l' => 'calc_eduform'])
        ->where([
            'visible' => 1
        ])
        ->all();

        foreach($mdEduforms as $eduform) {
            $eduforms[$eduform['id']] = $eduform['name'];
        }

        $services = (new Query())
		->select([
            'lessonPaied' => 'sum(i.num)',
            'serviceId' => 's.id',
            'serviceName' => 's.name',
            'languageId' => 's.calc_lang',
            'eduformId' => 's.calc_eduform'
        ])
		->from([ 'i' => 'calc_invoicestud'])
		->innerJoin(['s' => 'calc_service'], 's.id = i.calc_service')
		->where([
            'i.calc_studname' => $this->id,
            'i.visible' => 1,
        ])
		->andFilterWhere(['in', 'i.calc_service', $ids])
		->groupBy(['serviceid', 'servicename', 'languageId', 'eduformId'])
		->orderBy(['s.data' => SORT_DESC])
        ->all();

        foreach($services as &$service) {
            $service['languageName'] = $languages[$service['languageId']] ?? '';
            $service['eduformName'] = $eduforms[$service['eduformId']] ?? '';
        }
        
        return $services;
    }

    public function getSchedule()
    {
		$schedule = (new Query())
		->select([
            'coursename' => 'l.name',
            'denname' => 'd.name',
            'starttime' => 'sc.time_begin',
            'endtime' => 'sc.time_end',
            'office' => 'o.name',
            'cabinet' => 'c.name',
            'teachername' => 't.name'
        ])
		->from(['s' => 'calc_service'])
		->innerJoin(['gt' => 'calc_groupteacher'], 's.id = gt.calc_service')
		->innerJoin(['t' => 'calc_teacher'], 't.id = gt.calc_teacher')
		->innerJoin(['sc' => 'calc_schedule'], 'sc.calc_teacher = t.id AND sc.calc_groupteacher = gt.id')
		->innerJoin(['d' => 'calc_denned'], 'd.id = sc.calc_denned')
		->innerJoin(['o' => 'calc_office'], 'o.id = sc.calc_office')
		->innerJoin(['c' => 'calc_cabinetoffice'], 'sc.calc_cabinetoffice = c.id')
		->innerJoin(['sg' => 'calc_studgroup'], 'gt.id = sg.calc_groupteacher')
		->innerJoin(['sn'=> 'calc_studname'], 'sn.id = sg.calc_studname')
		->innerJoin(['l' => 'calc_lang'], 'l.id = s.calc_lang')
		->where([
            'sn.id' => $this->id,
            'gt.visible' => 1,
            'sg.visible' => 1
        ])
		->orderBy([
            'd.id' => SORT_ASC,
            'sc.time_begin' => SORT_ASC
        ])
        ->all();
        
        return $schedule;
    }

    public function getMessages()
    {
        $query = (new Query())
        ->select([
            'id'        => 'm.id',
            'date'     => 'm.data',
            'sender'   => 'u1.name',
            'receiver' => 'u2.name',
            'text'     => 'm.description',
            'title'    => 'm.name',
            'type'     => 'm.calc_messwhomtype',
        ])
        ->from(['m' => 'calc_message'])
        ->leftjoin(['u1' => 'users'], 'u1.id = m.user')
		->leftjoin(['u2' => 'users'], 'u2.id = m.refinement_id')
        ->where([
            'm.send' => 1,
            'm.visible' => 1,
        ])
        ->andWhere([
            'or',
            ['m.calc_messwhomtype' => 100, 'user' => $this->id],
            ['m.calc_messwhomtype' => 13, 'm.refinement_id' => $this->id],
        ])
        ->orderBy(['m.data' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
    }

    public function calculateBalance($dolg2 = 0, $services = [], $lessons = [])
    {
        $dolg1 = 0;
        $totalpayedlessons = 0;
        $totalattendedlessons = 0;
        foreach ($services as $lesspaied) {
            $cost = (new Query())
            ->select(['value' => '(i.value / i.num)'])
            ->from(['i' => 'calc_invoicestud'])
            ->where([
                'i.calc_studname' => $this->id,
                'i.visible' => 1,
                'i.calc_service' => $lesspaied['serviceId']
            ])
            ->orderBy(['i.id' => SORT_DESC])
            ->limit(1)
            ->one();
            foreach ($lessons as $lessinfo) {
                $servdolg = 0;
                if ((int)$lesspaied['serviceId'] === (int)$lessinfo['serviceId']) {
                    $totalpayedlessons += $lesspaied['lessonPaied'];
                    $totalattendedlessons += $lessinfo['lessonAttend'];
                    $lesscount = $lesspaied['lessonPaied'] - $lessinfo['lessonAttend'];
                    if ($lesscount < 0) {
                        // считаем и присваиваем стоимость урока
                        $totalcost = $cost['value'];
                        // считаем сумму за все уроки одной группы
                        $servdolg = $lesscount * $totalcost;
                    }
                }
                // считаем сумму по всем группам
                $dolg1 = $dolg1 + $servdolg;
            }
        }
        return $dolg1 + $dolg2;
    }

    public function getTeachers()
    {
        $teachersRawIds = (new Query())
        ->select([
            'id' => 'tg.calc_teacher',
        ])
		->from(['sn' => 'calc_studname'])
		->innerJoin(['sg' => 'calc_studgroup'], 'sn.id = sg.calc_studname')
        ->innerJoin(['gt' => 'calc_groupteacher'], 'gt.id = sg.calc_groupteacher')
        ->innerJoin(['tg' => 'calc_teachergroup'], 'tg.calc_groupteacher = gt.id')
		->where([
            'sn.id' => $this->id,
            'sg.visible' => 1,
            'tg.visible' => 1,
        ])
        ->all();

        $teachers = [];
        $teachersIds = [];
        foreach ($teachersRawIds as $teaherId) {
            $teachersIds[] = $teaherId['id'];
        }
        $teachersIds = array_unique($teachersIds);
        
        if (!empty($teachersIds)) {
            $teachersInfo = (new Query())
            ->select([
                'id' => 'u.id',
                'name' => 't.name',
                'photo' => 'u.logo',
                'tid' => 't.id',
            ])
            ->from(['t' => 'calc_teacher'])
            ->innerJoin(['u' => 'users'], 'u.calc_teacher = t.id')
            ->where([
                't.id' => $teachersIds
            ])
            ->orderBy(['t.name' => SORT_ASC])
            ->all();
    
            foreach($teachersInfo as $teacherInfo) {
                $teachers[$teacherInfo['tid']] = [
                    'id' => $teacherInfo['id'],
                    'name' => $teacherInfo['name'],
                    'languages' => [],
                    'photo' => $teacherInfo['photo'],
                ];
            }

            $teachersLanguages = (new Query())
            ->select([
                'id' => 'lt.calc_teacher',
                'name' => 'l.name',
            ])
            ->from(['l' => 'calc_lang'])
            ->innerJoin(['lt' => 'calc_langteacher'], 'l.id = lt.calc_lang')
            ->where([
                'lt.calc_teacher' => $teachersIds,
                'lt.visible' => 1,
            ])
            // 16 = без привязки к языку
            ->andWhere(['!=', 'l.id', 16])
            ->orderBy(['l.name' => SORT_ASC])
            ->all();

            foreach($teachersLanguages as $teacherLanguage) {
                if (isset($teachers[$teacherLanguage['id']])) {
                    $teachers[$teacherLanguage['id']]['languages'][] = $teacherLanguage['name'];
                }
            }
        }

        return $teachers;
    }

    public function availableMessageReceiversList()
    {
		//выбираем руководителей
		$chiefs = (new Query())
		->select(['uid' => 'u.id', 'title' => 'u.name'])
		->from(['u' => 'users'])
		->where([
            'u.visible' => 1,
            'u.status' => 3,
            'u.id' => [7, 41],
        ])
		->orderBy(['u.id' => SORT_ASC])
		->all();

		//выбираем список преподавателей активных курсов
		$teachers = (new Query())
		->select('ctch.name as title, u.id as uid')
		->from('calc_studname csn')
		->leftjoin('calc_studgroup csg', 'csn.id=csg.calc_studname')
		->leftjoin('calc_groupteacher cgt', 'cgt.id=csg.calc_groupteacher')
		->leftjoin('calc_service csv', 'csv.id=cgt.calc_service')
		->leftjoin('calc_lang cl', 'cl.id=csv.calc_lang')
		->leftjoin('calc_teacher ctch', 'ctch.id=cgt.calc_teacher')
		->leftjoin('users u', 'u.calc_teacher=ctch.id')
		->leftjoin('roles st', 'u.status=st.id')
		->where([
            'csn.id' => $this->id,
            'cgt.visible' => 1,
            'u.visible' => 1,
        ])
		->orderBy(['u.id' => SORT_ASC])
		->all();
		
		//выбираем список менеджеров офисов где провоходят занятия активных курсов 
		$managers = (new Query())
		->select('u.id as uid, u.name as title')
		->from('calc_studname csn')
		->leftjoin('calc_studgroup csg', 'csn.id=csg.calc_studname')
		->leftjoin('calc_groupteacher cgt', 'cgt.id=csg.calc_groupteacher')
		->leftjoin('calc_service csv', 'csv.id=cgt.calc_service')
		->leftjoin('calc_office cof', 'cof.id = cgt.calc_office')
		->leftjoin('users u', 'u.calc_office=cof.id')
		->leftjoin('roles st', 'u.status=st.id')
		->where([
            'csn.id' => $this->id,
            'cgt.visible' => 1,
            'u.visible' => 1,
        ])
		->orderBy(['cgt.data' => SORT_DESC])
		->all();

		$allusers = array_merge($chiefs, $teachers, $managers);
		$users = [];
		foreach ($allusers as $user){
			$users[$user['uid']] = $user['title'];
		}
		return array_unique($users);
    }

    /**
     * Количество успешиков клиента (полученные минус списанные)
     * @return int
     */
    public function getSuccessesCount() : int
    {
        return $this->getReceivedSuccessesCount() - $this->getSpendSuccessesCount();
    }

    /**
     * Количество успешиков полученных клиентом за занятиям
     * @return int
     */
    public function getReceivedSuccessesCount() : int
    {
        $count = (new \yii\db\Query())
            ->select(['successes' => 'SUM(sjg.successes)'])
            ->from(['sjg' => 'calc_studjournalgroup'])
            ->innerJoin(['jg' => 'calc_journalgroup'], 'jg.id = sjg.calc_journalgroup')
            ->andWhere([
                'sjg.calc_studname' => $this->id,
                'sjg.calc_statusjournal' => 1,
                'jg.visible' => 1,
            ])
            ->one();

        return $count['successes'] ?? 0;
    }

    /**
     * Количество успешиков полученных клиентом за занятиям
     * @return int
     */
    public function getSpendSuccessesCount() : int
    {
        $count = (new \yii\db\Query())
            ->select(['successes' => 'SUM(ss.count)'])
            ->from(['ss' => 'spend_successes'])
            ->andWhere([
                'ss.student_id' => $this->id,
                'ss.visible' => 1,
            ])
            ->one();

        return $count['successes'] ?? 0;
    }

    /**
     * @param int $count
     * @return array
     */
    public static function prepareStudentSuccessesList(int $count): array
    {
        $successes = [];
        if ($count > 0) {
            for ($num = 1; $num <= $count; $num++) {
                $successes[] = Html::tag('i', '', ['class' => 'fas fa-ticket-alt', 'aria-hidden' => 'true', 'title' => 'Успешик', 'style' => 'margin-right:2px']);
            }
        }

        return $successes;
    }
}