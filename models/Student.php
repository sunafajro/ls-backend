<?php

namespace app\models;

use Yii;
use app\traits\StudentMergeTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "calc_studname".
 *
 * @property integer $id
 * @property string $name
 * @property string $fname
 * @property string $lname
 * @property string $mname
 * @property string $birthdate
 * @property string $email
 * @property string $address
 * @property integer $visible
 * @property integer $history
 * @property string $phone
 * @property double $debt
 * @property double $debt2
 * @property double $invoice
 * @property double $money
 * @property integer $calc_sex
 * @property integer $calc_cumulativediscount
 * @property integer $active
 * @property integer $calc_way
 * @property string $description
 * 
 * @property Contract[]   $contracts
 * @property ClientAccess $studentLogin
 */
class Student extends ActiveRecord
{

    use StudentMergeTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_studname';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'visible', 'history', 'calc_sex','active'], 'required'],
            [['name', 'fname', 'lname', 'mname', 'email', 'phone', 'address', 'description'], 'string'],
            [['visible', 'history', 'calc_sex', 'calc_cumulativediscount', 'active', 'calc_way'], 'integer'],
            [['debt', 'debt2', 'invoice', 'money'], 'number'],
            [['birthdate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Full name'),
            'fname' => Yii::t('app', 'First name'),
            'lname' => Yii::t('app', 'Last name'),
            'mname' => Yii::t('app', 'Middle name'),
            'birthdate' => Yii::t('app', 'Birthdate'),
            'email' => Yii::t('app', 'Email'),
            'address' => Yii::t('app', 'Address'),
            'visible' => 'Visible',
            'history' => 'History',
            'phone' => Yii::t('app', 'Phone'),
            'debt' => Yii::t('app', 'Debt'),
            'invoice' => 'Invoice',
            'money' => 'Money',
            'calc_sex' => Yii::t('app', 'Sex'),
            'calc_cumulativediscount' => 'Calc Cumulativediscount',
            'active' => 'Active',
            'calc_way' => Yii::t('app','Way to Attract'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    public function getContracts() : ActiveQuery
    {
        return $this->hasMany(Contract::class, ['student_id' => 'id'])->andWhere(['visible' => 1]);
    }

    public function getStudentLogin(): ActiveQuery
    {
        return $this->hasOne(ClientAccess::class, ['calc_studname' => 'id']);
    }

    /**
     *  метод возвращает сумму по счетам выставленным клиенту
     */

    public function getStudentTotalInvoicesSum()
    {
        $invoices_sum = (new Query())
        ->select(['money' => 'sum(value)'])
        ->from(Invoicestud::tableName())
        ->where([
            'visible' => 1,
            'calc_studname' => $this->id,
            'remain' => [Invoicestud::TYPE_NORMAL, Invoicestud::TYPE_REMAIN]
        ])
        ->one();
        
        return round($invoices_sum['money']) ?? 0;
    }

    /**
    *  метод возвращает сумму по оплатам принятым от клиента
    */

    public function getStudentTotalPaymentsSum()
    {
        $payments_sum = (new Query())
        ->select('sum(value) as money')
        ->from(Moneystud::tableName())
        ->where([
            'visible' => 1,
            'calc_studname' => $this->id
        ])
        ->one();

        return round($payments_sum['money']) ?? 0;
    }

    /**
     * обновляет сумму счет оплат и баланс студента
     * @param integer $id
     * @return boolean
     */
    public function updateInvMonDebt()
    {
        $this->invoice = $this->getStudentTotalInvoicesSum();
        $this->money   = $this->getStudentTotalPaymentsSum();
        $this->debt    = $this->money - $this->invoice;

        /* если баланс меньше 1 рубля в обе стороны, привести к 0 */
        if (abs($this->debt) <= 1) {
            $this->debt = 0;
        }

        return $this->save(true, ['invoice', 'money', 'debt']);
    }

    /**
     *  метод отдает список студентов в виде одномерного массива
     */
    public static function getStudetnsListSimple()
    {
        $students = [];
        $students_obj = Student::find()->select(['id' => 'id', 'name' => 'name'])->where('visible=:one', [':one' => 1])->orderby(['id'=>SORT_ASC])->all();
        foreach($students_obj as $s) {
            $students[$s->id] = '#' . $s->id . ' ' . $s->name;
        }

        return $students;
    }

    public static function getStudentDetailsColumns()
    {
        return [
            [
                'id' => 'id',
                'name' => '№',
                'show' => false                            
            ],
            [
                'id' => 'date',
                'name' => Yii::t('app', 'Date'),
                'show' => true
            ],
            [
                'id' => 'type',
                'name' => Yii::t('app', 'Type'),
                'show' => true
            ],
            [
                'id' => 'name',
                'name' => Yii::t('app', 'Name'),
                'show' => true
            ],
            [
                'id' => 'num',
                'name' => Yii::t('app', 'Count'),
                'show' => true
            ],
            [
                'id' => 'sum',
                'name' => Yii::t('app', 'Sum'),
                'show' => true
            ],
            [
                'id' => 'receipt',
                'name' => Yii::t('app', 'Receipt'),
                "show" => true
            ]
        ];
    }

    public static function getStudentDetailsRows($id = null)
    {
        $result = [];
        if ($id) {
            $invoises = Invoicestud::getStudentInvoiceByIdBrief($id);
            $payments = Moneystud::getStudentPaymentByIdBrief($id);
            $result = array_merge($payments, $invoises);
            // сортируем по двум колонкам
            $date = [];
            $name = [];
            foreach($result as $key => $row) {
                $date[$key] = isset($row['date']) ? $row['date'] : '';
                $name[$key] = isset($row['name']) ? $row['name'] : '';
                $result[$key]['type'] = isset($row['name']) && isset($row['num']) ? 'счёт' : 'оплата';
            }
            array_multisort($date, SORT_DESC, $name, SORT_ASC, $result);
        }
        return $result;
    }

    public function getStudentOffices()
    {
        return (new Query())
        ->select(['id' => 'o.id', 'name' => 'o.name', 'isMain' => 'so.is_main'])
        ->from(['so' => 'student_office'])
        ->innerJoin('calc_office o', 'o.id=so.office_id')
        ->where(['so.student_id' => $this->id])
        ->orderBy(['is_main' => SORT_DESC, 'o.name' => SORT_ASC])
        ->all();
    }

    public function getStudentSales() : array
    {
        return (new Query())
            ->select([
                'id'       => 'ss.id',
                'name'     => 's.name',
                'type'     => 's.procent',
                'value'    => 's.value',
                'visible'  => 'ss.visible',
                'date'     => 'ss.data',
                'user'     => 'u.name',
                'reason'   => 'ss.reason',
                'approved' => 'ss.approved',
            ])
            ->from(['ss' => Salestud::tableName()])
            ->innerJoin(['s' => Sale::tableName()], 'ss.calc_sale = s.id')
            ->innerJoin(['u' => User::tableName()], 'ss.user = u.id')
            ->where([
                'ss.visible' => 1,
                'ss.calc_studname' => $this->id
            ])
            ->andWhere(['!=', 's.procent', Sale::TYPE_PERMAMENT])
            ->orderBy(['ss.visible' => SORT_DESC, 's.procent' => SORT_ASC, 's.value' => SORT_ASC])
            ->all();
    }

    public function getStudentAvailabelSales(array $params) : array
    {
        $search = $params['term'] ?? NULL;
        if ($search) {
            $search = str_replace(',', '.', $search);
        }
        $columnName = 's.name';
        $operator = 'like';
        if ((int)$search !== 0) {
            $columnName = 's.value';
            $operator   = '=';
        }
        $usedSales = (new Query())
        ->select(['id' => 'calc_sale'])
        ->from(Salestud::tableName())
        ->where([
            'visible'       => 1,
            'calc_studname' => $this->id
        ])
        ->all();
        $salesRaw = (new Query())
        ->select(['id' => 's.id', 'name' => 's.name', 'type' => 's.procent', 'value' => 's.value'])
        ->from(['s' => Sale::tableName()])
        ->where(['s.visible' => 1])
        ->andWhere(['!=', 's.procent', Sale::TYPE_PERMAMENT])
        ->andFilterWhere(['not in', 's.id', ArrayHelper::getColumn($usedSales, 'id')])
        ->andFilterWhere([$operator, $columnName, $search])
        ->orderBy(['s.name' => SORT_ASC])
        ->limit(10)
        ->all();

        $sales = [];
        foreach($salesRaw as $sale) {
            $sales[] = [
                'label' => $sale['name'] . ' :: ' . $sale['value'] . ((int)$sale['type'] === Sale::TYPE_RUB ? ' р.' : '%'),
                'value' => $sale['id'],
            ];
        }
        return $sales;
    }

    public static function getDebtsTotalSum(string $office = null) : float
    {
        $debt = (new \yii\db\Query())
        ->select(['debt' => 's.debt'])
        ->from(['s' => Student::tableName()]);
        if ($office) {
            $debt = $debt
                ->innerJoin(['so' => 'student_office'], 's.id = so.student_id')
                ->andWhere(['so.office_id' => $office]);
        }
        $result = $debt
            ->andWhere([
                's.active' => 1,
                's.visible' => 1
            ])
            ->andWhere(['<', 's.debt', 0])
            ->sum('debt');

        return $result ?? 0;
    }

    public static function getStudentsAutocomplete(string $term = NULL) : array
    {
        $whereClause = ['like', 'name', $term];
        // проверим возможно в запросе id, а не ФИО
        if (!preg_match( '/[^0-9]/',$term)) {
            $whereClause = ['id' => (int)$term];
        }
        return (new Query())
        ->select(['label' => 'CONCAT("#", id, " ", name, " ", COALESCE(DATE_FORMAT(birthdate, "%d.%m.%y"), ""), " ", "(", phone, ")")', 'value' => 'id'])
		->from(Student::tableName())
        ->where([
            'visible' => 1
        ])
        ->andFilterWhere($whereClause)
        ->limit(15)
        ->all();
    }

    public function getServicesBalance(int $serviceId = null, array $schedule = null)
    {
        // запрашиваем услуги назначенные студенту
        $services = (new \yii\db\Query())
        ->select('s.id as sid, s.name as sname, SUM(is.num) as num')
        ->distinct()
        ->from(['s' => Service::tableName()])
        ->leftjoin(['is' => Invoicestud::tableName()], 'is.calc_service = s.id')
        ->where([
            'is.remain' => [
                Invoicestud::TYPE_NORMAL,
                Invoicestud::TYPE_NETTING
            ],
            'is.visible' => 1,
            'is.calc_studname' => $this->id,
        ])
        ->andFilterWhere(['s.id' => $serviceId])
        ->groupby(['is.calc_studname', 's.id'])
        ->orderby(['s.id' => SORT_ASC])
        ->all();
        
        // проверяем что у студента есть назначенные услуги
        if (!empty($services)) {
            $i = 0;
            // распечатываем массив
            foreach($services as $service){
                // запрашиваем из базы колич пройденных уроков
                $lessons = (new \yii\db\Query())
                ->select('COUNT(sjg.id) AS cnt')
                ->from(['sjg' => 'calc_studjournalgroup'])
                ->leftjoin(['gt' => Groupteacher::tableName()], 'sjg.calc_groupteacher = gt.id')
                ->leftjoin(['jg' => Journalgroup::tableName()], 'sjg.calc_journalgroup = jg.id')
                ->where([
                    'jg.view'                => 1,
                    'jg.visible'             => 1,
                    'sjg.calc_statusjournal' => [
                        Journalgroup::STUDENT_STATUS_PRESENT,
                        Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED
                    ],
                    'gt.calc_service'        => $service['sid'],
                    'sjg.calc_studname'      => $this->id,
                ])
                ->one();
                
                // считаем остаток уроков
                $cnt = $services[$i]['num'] - $lessons['cnt'];
                $services[$i]['num'] = $cnt;
                if (!empty($schedule)) {
                    $services[$i]['npd'] = Moneystud::getNextPaymentDay($schedule, $service['sid'], $cnt);
                } else {
                    $services[$i]['npd'] = 'none';
                }
                $i++;
            }
        }

        return $services;
    }

    public function getStudentLoginStatus() : array
    {
        /** @var ClientAccess $studentLogin */
        $studentLogin = $this->studentLogin ?? null;
        $status = [
            'id'            => $studentLogin->id ?? null,
            'hasLogin'      => $studentLogin->id ?? false,
            'lastLoginDate' => null,
            'loginActive'   => false,
        ];
        if ($studentLogin) {
            $status['lastLoginDate'] = $studentLogin->date;
            $loginLimitDate = date('Y-m-d', strtotime('-2 month'));
            if ($studentLogin->date > $loginLimitDate && (int)$this->active === 1) {
                $status['loginActive'] = true;
            }
        }

        return $status;
    }

    /**
     *  метод переносит данные из профиля студента с id2 в профиль студента с id1
     */

    public static function mergeStudentAccounts(int $id1, int $id2) : array
    {
        $result = [];
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
                $result['update_calls']            = Call::mergeStudents($id1, $id2);
                $result['update_contracts']        = Contract::mergeStudents($id1, $id2, 'student_id');
                $result['update_invoices']         = Invoicestud::mergeStudents($id1, $id2);
                $result['update_messages']         = Message::mergeStudents($id1, $id2, 'user', ['calc_messwhomtype' => 100]);
                $result['update_payments']         = Moneystud::mergeStudents($id1, $id2);
                $result['update_sales']            = Salestud::mergeStudents($id1, $id2);
                $result['update_grades']           = StudentGrade::mergeStudents($id1, $id2);
                $result['update_groups']           = Studgroup::mergeStudents($id1, $id2);
                $result['update_journals']         = Studjournalgroup::mergeStudents($id1, $id2);
                $result['update_journals_history'] = Studjournalgrouphistory::mergeStudents($id1, $id2);
                $result['update_studphones']       = Studphone::mergeStudents($id1, $id2);
                $result['update_logins_log']       = Studloginlog::mergeStudents($id1, $id2);
                $result['update_clientaccess']     = ClientAccess::mergeClientAccounts($id1, $id2);
                $result['update_sms_log']          = Smslog::mergeStudents($id1, $id2);
                $result['update_studname_history'] = Studnamehistory::mergeStudents($id1, $id2);
                $result['update_offices']          = self::mergeStudents($id1, $id2, 'student_id', [], 'student_office');
                $result['update_receipts']         = Receipt::mergeStudents($id1, $id2, 'student_id');
        }
        return $result;
    }    
}
