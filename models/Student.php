<?php

namespace app\models;

use Yii;
use app\traits\StudentMergeTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "calc_studname".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $fname
 * @property string  $lname
 * @property string  $mname
 * @property string  $birthdate
 * @property string  $email
 * @property string  $address
 * @property integer $visible
 * @property integer $history
 * @property string  $phone
 * @property float   $debt
 * @property float   $debt2
 * @property float   $invoice
 * @property float   $money
 * @property float   $commission
 * @property integer $calc_sex
 * @property integer $calc_cumulativediscount
 * @property integer $active
 * @property integer $calc_way
 * @property string  $description
 * @property string  $settings
 * 
 * @property Contract[]    $contracts
 * @property ClientAccess  $studentLogin
 * @property Invoicestud[] $invoices
 * @property Moneystud[]   $payments
 */
class Student extends ActiveRecord
{
    use StudentMergeTrait;

    const DETAIL_TYPE_INVOICES                  = 1;
    const DETAIL_TYPE_PAYMENTS                  = 2;
    const DETAIL_TYPE_INVOICES_PAYMENTS         = 3; // по умолчанию
    const DETAIL_TYPE_LESSONS                   = 4;
    const DETAIL_TYPE_INVOICES_LESSONS          = 5;
    const DETAIL_TYPE_PAYMENTS_LESSONS          = 6;
    const DETAIL_TYPE_INVOICES_PAYMENTS_LESSONS = 7;

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
            [['debt', 'debt2', 'invoice', 'money', 'commission'], 'number'],
            [['birthdate', 'settings'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                      => 'ID',
            'name'                    => Yii::t('app', 'Full name'),
            'fname'                   => Yii::t('app', 'First name'),
            'lname'                   => Yii::t('app', 'Last name'),
            'mname'                   => Yii::t('app', 'Middle name'),
            'birthdate'               => Yii::t('app', 'Birthdate'),
            'email'                   => Yii::t('app', 'Email'),
            'address'                 => Yii::t('app', 'Address'),
            'visible'                 => Yii::t('app', 'Visible'),
            'history'                 => Yii::t('app', 'History'),
            'phone'                   => Yii::t('app', 'Phone'),
            'debt'                    => Yii::t('app', 'Debt'),
            'invoice'                 => Yii::t('app', 'Invoices sum'),
            'money'                   => Yii::t('app', 'Payments sum'),
            'commission'              => Yii::t('app', 'Commission'),
            'calc_sex'                => Yii::t('app', 'Sex'),
            'calc_cumulativediscount' => Yii::t('app', 'Cumulative discount'),
            'active'                  => Yii::t('app', 'Active'),
            'calc_way'                => Yii::t('app','Way to Attract'),
            'description'             => Yii::t('app', 'Description'),
            'settings'                => Yii::t('app', 'Settings'),
        ];
    }

    /**
     * Список типов отчета детализации
     *
     * @return array
     */
    public static function getDetailTypes()
    {
        return [
            self::DETAIL_TYPE_INVOICES                  => Yii::t('app', 'Invoices'),
            self::DETAIL_TYPE_PAYMENTS                  => Yii::t('app', 'Payments'),
            self::DETAIL_TYPE_INVOICES_PAYMENTS         => Yii::t('app', 'Invoices/Payments'),
            self::DETAIL_TYPE_LESSONS                   => Yii::t('app', 'Lessons'),
            self::DETAIL_TYPE_INVOICES_LESSONS          => Yii::t('app', 'Invoices/Lessons'),
            self::DETAIL_TYPE_PAYMENTS_LESSONS          => Yii::t('app', 'Payments/Lessons'),
            self::DETAIL_TYPE_INVOICES_PAYMENTS_LESSONS => Yii::t('app', 'Invoices/Payments/Lessons'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContracts() : ActiveQuery
    {
        return $this->hasMany(Contract::class, ['student_id' => 'id'])->andWhere(['visible' => 1]);
    }

    /**
     * @return ActiveQuery
     */
    public function getStudentLogin(): ActiveQuery
    {
        return $this->hasOne(ClientAccess::class, ['calc_studname' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoicestud::class, ['calc_studname' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Moneystud::class, ['calc_studname' => 'id']);
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

    public function getStudentTotalCommissionsSum()
    {
        $commissions_sum = (new Query())
        ->select('sum(value) as money')
        ->from(StudentCommission::tableName())
        ->where([
            'visible' => 1,
            'student_id' => $this->id
        ])
        ->one();

        return round($commissions_sum['money']) ?? 0;
    }

    /**
     * обновляет сумму счет оплат и баланс студента
     * @param integer $id
     * @return boolean
     */
    public function updateInvMonDebt()
    {
        $this->invoice    = $this->getStudentTotalInvoicesSum();
        $this->money      = $this->getStudentTotalPaymentsSum();
        $this->commission = $this->getStudentTotalCommissionsSum();

        $this->debt       = $this->money - ($this->invoice + $this->commission);

        /* если баланс меньше 1 рубля в обе стороны, привести к 0 */
        if (abs($this->debt) <= 1) {
            $this->debt = 0;
        }

        return $this->save(true, ['invoice', 'money', 'debt', 'commission']);
    }

    /**
     * Подсчет результата для итоговой колонки таблицы детализации
     * @param array  $detailData
     * @param string $column
     * @param bool   $moneyFormat
     *
     * @return string
     */
    public static function calculateDetailTotal(array $detailData, string $column, bool $moneyFormat = false)
    {
        $result = [
            'invoice' => 0,
            'payment' => 0,
            'lesson'  => 0,
        ];
        foreach ($detailData as $row) {
            if (in_array($row['type'], ['invoice', 'payment', 'lesson']) && $row[$column] !== '') {
                $result[$row['type']] += $row[$column] ?? 0;
            }
        }

        $str = [];
        if ($column === 'num') {
            $str[] = Html::tag('b', 'по счетам:') . ' +' . $result['invoice'];
            $str[] = Html::tag('b', 'по журналам:') . ' -' . $result['lesson'];
            $str[] = Html::tag('b', 'итог:') . ' ' . ($result['invoice'] - $result['lesson']);
        } else if ($column === 'sum') {
            $str[] = Html::tag('b', 'по счетам:') . ' -' . ($moneyFormat
                    ? number_format($result['invoice'], 2, ',', ' ')
                    : $result['invoice']
                );
            $str[] = Html::tag('b', 'по оплатам:') . ' +' . ($moneyFormat
                    ? number_format($result['payment'], 2, ',', ' ')
                    : $result['payment']
                );
            $str[] = Html::tag('b', 'итог:') . ' ' . ($moneyFormat
                    ? number_format(($result['payment'] - $result['invoice']), 2, ',', ' ')
                    : ($result['payment'] - $result['invoice'])
                );
        }
        return join('<br />', $str);
    }

    /**
     * Возвращает данные для построения таблицы детализации
     * Тип:
     *     2 - Счета + оплаты,
     *     5 - Счета + занятия,
     *     7 - Счета + оплаты + занятия
     * Фильтры:
     *     start   string - дата начала (по умолчанию null)
     *     end     string - дата окончания (по умолчанию null)
     *     service int - услуга (по умолчанию null)
     * @param int   $type
     * @param array $filters
     * @param bool  $withTotal
     *
     * @return array
     */
    public function getDetails(int $type, array $filters = [], bool $withTotal = false)
    {
        $startOfYear = null;
        $endOfYear   = null;
        $service     = null;
        if (isset($filters['start']) && $filters['start']) {
            $startOfYear = $filters['start'];
        }
        if (isset($filters['end']) && $filters['end']) {
            $endOfYear = $filters['end'];
        }
        if (isset($filters['service']) && $filters['service']) {
            $service = $filters['service'];
        }

        $query = null;
        switch ($type) {
            case self::DETAIL_TYPE_INVOICES:
                $query = $this->getDetailInvoicesQuery($startOfYear, $endOfYear, $service);
                break;
            case self::DETAIL_TYPE_PAYMENTS:
                $query = $this->getDetailPaymentsQuery($startOfYear, $endOfYear);
                break;
            case self::DETAIL_TYPE_LESSONS:
                $query = $this->getDetailLessonsQuery($startOfYear, $endOfYear, $service);
                break;
            case self::DETAIL_TYPE_INVOICES_LESSONS:
                $invoices = $this->getDetailInvoicesQuery($startOfYear, $endOfYear, $service);
                $lessons = $this->getDetailLessonsQuery($startOfYear, $endOfYear, $service);
                $query = $invoices->union($lessons);
                break;
            case self::DETAIL_TYPE_PAYMENTS_LESSONS:
                $payments = $this->getDetailPaymentsQuery($startOfYear, $endOfYear);
                $lessons = $this->getDetailLessonsQuery($startOfYear, $endOfYear, $service);
                $query = $payments->union($lessons);
                break;
            case self::DETAIL_TYPE_INVOICES_PAYMENTS_LESSONS:
                $invoices = $this->getDetailInvoicesQuery($startOfYear, $endOfYear, $service);
                $payments = $this->getDetailPaymentsQuery($startOfYear, $endOfYear);
                $lessons = $this->getDetailLessonsQuery($startOfYear, $endOfYear, $service);
                $query = $invoices->union($payments)->union($lessons);
                break;
            default:
                $invoices = $this->getDetailInvoicesQuery($startOfYear, $endOfYear, $service);
                $payments = $this->getDetailPaymentsQuery($startOfYear, $endOfYear);
                $query = $invoices->union($payments);
        }

        return (new Query())->from($query)->orderBy(['date' => SORT_DESC])->all();
    }

    /**
     * @param string|null $startOfYear
     * @param string|null $endOfYear
     * @param int|null    $service
     *
     * @return Query
     */
    public function getDetailInvoicesQuery(string $startOfYear = null, string $endOfYear = null, int $service = null) : Query
    {
        return (new Query())
            ->select([
                'id' => 'i.id',
                'date' => 'i.data',
                'type' => new Expression("('invoice')"),
                'name' => new Expression("CONCAT('#', s.id, ' ', s.name)"),
                'num' => 'i.num',
                'sum' => 'i.value',
                'comment' => new Expression("('')"),
            ])
            ->from(['i' => Invoicestud::tableName()])
            ->innerJoin(['s' => Service::tableName()],'i.calc_service = s.id')
            ->where([
                'i.remain' => [
                    Invoicestud::TYPE_NORMAL,
                    Invoicestud::TYPE_NETTING
                ],
                'i.calc_studname' => $this->id,
                'i.visible' => 1
            ])
            ->andFilterWhere(['>=', 'i.data', $startOfYear])
            ->andFilterWhere(['<=', 'i.data', $endOfYear])
            ->andFilterWhere(['s.id' => $service]);
    }

    /**
     * @param string|null $startOfYear
     * @param string|null $endOfYear
     * @return Query
     */
    public function getDetailPaymentsQuery(string $startOfYear = null, string $endOfYear = null) : Query
    {
        return (new Query())
            ->select([
                'id'      => 'm.id',
                'date'    => 'm.data',
                'type'    => new Expression("('payment')"),
                'name'    => new Expression("('')"),
                'num'     => new Expression("('')"),
                'sum'     => 'm.value',
                'comment' => 'm.receipt'
            ])
            ->from(['m' => Moneystud::tableName()])
            ->where([
                'm.calc_studname' => $this->id,
                'm.visible' => 1
            ])
            ->andFilterWhere(['>=', 'm.data', $startOfYear])
            ->andFilterWhere(['<=', 'm.data', $endOfYear]);
    }

    /**
     * @param string|null $startOfYear
     * @param string|null $endOfYear
     * @param int |null   $service
     * @return Query
     */
    public function getDetailLessonsQuery(string $startOfYear = null, string $endOfYear = null, int $service = null) : Query
    {
        return (new Query())
            ->select([
                'id' => 'l.id',
                'date' => "l.data",
                'type' => new Expression("('lesson')"),
                'name' => new Expression("CONCAT('#', s.id, ' ', s.name)"),
                'num' => new Expression("('1')"),
                'sum' => new Expression("('')"),
                'comment' => new Expression("('')"),
            ])
            ->from(['l' => Journalgroup::tableName()])
            ->innerJoin(['sl' => Studjournalgroup::tableName()], 'sl.calc_journalgroup = l.id')
            ->innerJoin(['g' => Groupteacher::tableName()], 'l.calc_groupteacher = g.id')
            ->innerJoin(['s' => Service::tableName()], 'g.calc_service = s.id')
            ->andWhere([
                'l.visible' => 1,
                'l.view'    => 1,
                'sl.calc_statusjournal' => [
                    Journalgroup::STUDENT_STATUS_PRESENT,
                    Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED,
                ],
                'sl.calc_studname' => $this->id,
            ])
            ->andFilterWhere(['>=', 'l.data', $startOfYear])
            ->andFilterWhere(['<=', 'l.data', $endOfYear])
            ->andFilterWhere(['s.id' => $service]);
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
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
            ->innerJoin(['u' => BaseUser::tableName()], 'ss.user = u.id')
            ->where([
                'ss.visible' => 1,
                'ss.calc_studname' => $this->id
            ])
            ->andWhere(['!=', 's.procent', Sale::TYPE_PERMAMENT])
            ->orderBy(['ss.visible' => SORT_DESC, 's.procent' => SORT_ASC, 's.value' => SORT_ASC])
            ->all();
    }

    /**
     * @param array $params
     * @return array
     */
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

    /**
     * @param string|null $office
     * @return float
     */
    public static function getDebtsTotalSum(string $office = null) : float
    {
        $debt = (new Query())
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

    /**
     * @param string|null $term
     * @return array
     */
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

    /**
     * @param array $serviceId
     * @param array $schedule
     * 
     * @return array 
     */
    public function getServicesBalance(array $serviceId, array $schedule) : array
    {
        $services = Service::getStudentServicesByInvoices([$this->id], $serviceId);
        if (!empty($services)) {
            // распечатываем массив
            foreach($services as $i => $service){
                // запрашиваем из базы колич пройденных уроков
                $lessons = (new Query())
                ->select('COUNT(sjg.id) AS cnt')
                ->from(['sjg' => Studjournalgroup::tableName()])
                ->leftjoin(['gt' => Groupteacher::tableName()], 'sjg.calc_groupteacher = gt.id')
                ->leftjoin(['jg' => Journalgroup::tableName()], 'sjg.calc_journalgroup = jg.id')
                ->where([
                    'jg.view'                => 1,
                    'jg.visible'             => 1,
                    'sjg.calc_statusjournal' => [
                        Journalgroup::STUDENT_STATUS_PRESENT,
                        Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED
                    ],
                    'gt.calc_service'        => $service['id'],
                    'sjg.calc_studname'      => $this->id,
                ])
                ->one();
                
                // считаем остаток уроков
                $cnt = $services[$i]['num'] - $lessons['cnt'];
                $services[$i]['num'] = $cnt;
                if (!empty($schedule)) {
                    $services[$i]['npd'] = Moneystud::getNextPaymentDay($schedule, $service['id'], $cnt);
                } else {
                    $services[$i]['npd'] = 'none';
                }
            }
        }

        return $services;
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
        $count = (new Query())
            ->select(['successes' => 'SUM(sjg.successes)'])
            ->from(['sjg' => Studjournalgroup::tableName()])
            ->innerJoin(['jg' => Journalgroup::tableName()], 'jg.id = sjg.calc_journalgroup')
            ->andWhere([
                'sjg.calc_studname' => $this->id,
                'sjg.calc_statusjournal' => Journalgroup::STUDENT_STATUS_PRESENT,
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
        $count = (new Query())
            ->select(['successes' => 'SUM(ss.count)'])
            ->from(['ss' => SpendSuccesses::tableName()])
            ->andWhere([
                'ss.student_id' => $this->id,
                'ss.visible' => 1,
            ])
            ->one();

        return $count['successes'] ?? 0;
    }

    public function getSpendSuccessesHistory()
    {
        return (new Query())
            ->select([
                'count' => 'ss.count',
                'cause' => 'ss.cause',
                'user_id' => 'ss.user_id',
                'user_name' => 'u.name',
                'created_at' => 'ss.created_at',
            ])
            ->from(['ss' => SpendSuccesses::tableName()])
            ->innerJoin(['u' => BaseUser::tableName()], 'u.id = ss.user_id')
            ->where([
                'ss.student_id' => $this->id,
                'ss.visible' => 1
            ])
            ->all();
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
     * @param int $serviceId
     * @param string $action
     * 
     * @return bool
     */
    public function updateServicesList(int $serviceId, string $action) : bool
    {
        $settings = $this->settings ?? [];
        $services = $settings['hiddenServices'] ?? [];
        if ($action === 'hide') {
            if (!in_array($serviceId, $services)) {
                $services[] = $serviceId;
            }
        } else if ($action === 'show') {
            if (($index = array_search($serviceId, $services)) !== false) {
                unset($services[$index]);
            }
        }
        $settings['hiddenServices'] = $services;
        $this->settings = $settings;

        return $this->save(true, ['settings']);
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
                $result['update_commissions']      = StudentCommission::mergeStudents($id1, $id2, 'student_id');
        }
        return $result;
    }    
}
