<?php

namespace app\models;

use Yii;
use app\traits\StudentMergeTrait;
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
            //[['birthdate'], 'date', 'format'=>'yyyy-mm-dd'],
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
        return $invoices_sum['money'] ?? 0;
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
        return $payments_sum['money'] ?? 0;
    }

    /**
     * обновляет сумму счет оплат и баланс студента
     * @param integer $id
     * @return boolean
     */
    public function updateInvMonDebt()
    {
        $this->invoice = $this->getStudentTotalInvoicesSum();
        $this->money = $this->getStudentTotalPaymentsSum();
        $this->debt = $this->money - $this->invoice;
        if ($this->save()) {
            return true;
        } else {
            return false;
        }
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
        $sales = (new Query())
        ->select([
            'id' => 'ss.id',
            'name' => 's.name',
            'type' => 's.procent',
            'value' => 's.value',
            'visible' => 'ss.visible',
            'date' => 'ss.data',
            'user' => 'u.name',
        ])
        ->from(['ss' => Salestud::tableName()])
        ->innerJoin(['s' => Sale::tableName()], 'ss.calc_sale=s.id')
        ->leftJoin(['u' => User::tableName()], 'ss.user=u.id')
        ->where(['ss.calc_studname' => $this->id])
        ->andWhere(['!=', 's.procent', Sale::TYPE_PERMAMENT])
        ->orderBy(['ss.visible' => SORT_DESC, 's.procent' => SORT_ASC, 's.value' => SORT_ASC])
        ->all();
        return $sales;
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
            $operator = '=';
        }
        $usedSales = (new Query())
        ->select(['id' => 'calc_sale'])
        ->from(Salestud::tableName())
        ->where(['calc_studname' => $this->id])
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
     *  метод переносит данные из профиля студента с id2 в профиль студента с id1
     */

    public static function mergeStudentAccounts(int $id1, int $id2) : array
    {
        $result = [];
        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
            $result['update_calls']            = Call::mergeStudents($id1, $id2);
            $result['update_contracts']        = Contract::mergeStudents($id1, $id2);
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
        }
        return $result;
    }    
}
