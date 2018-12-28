<?php

namespace app\models;

use Yii;
use app\models\Call;
use app\models\Invoicestud;
use app\models\Moneystud;
use app\models\Salestud;
use app\models\Studgroup;
use app\models\Studjournalgroup;
use app\models\Studjournalgrouphistory;
use app\models\Message;
use app\models\Studphone;
use app\models\Studloginlog;
use app\models\ClientAccess;
use app\models\Smslog;
use app\models\Studnamehistory;

/**
 * This is the model class for table "calc_studname".
 *
 * @property integer $id
 * @property string $name
 * @property string $fname
 * @property string $lname
 * @property string $mname
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
class Student extends \yii\db\ActiveRecord
{
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
            [['debt', 'debt2', 'invoice', 'money'], 'number']
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

    public static function getStudentTotalInvoicesSum($id)
    {
        $invoices_sum = (new \yii\db\Query())
        ->select('sum(value) as money')
        ->from('calc_invoicestud')
        ->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();
        

        return $invoices_sum['money'] !== NULL ? $invoices_sum['money'] : 0;
    }

    /**
    *  метод возвращает сумму по оплатам принятым от клиента
    */

    public static function getStudentTotalPaymentsSum($id)
    {
        $payments_sum = (new \yii\db\Query())
        ->select('sum(value) as money')
        ->from('calc_moneystud')
        ->where('visible=:vis and calc_studname=:sid', [':vis'=>1, ':sid'=>$id])
        ->one();

        return $payments_sum['money'] !== NULL ? $payments_sum['money'] : 0;
    }

    /**
     * обновляет сумму счет оплат и баланс студента
     * @param integer $id
     * @return boolean
     */
    public static function updateInvMonDebt($id)
    {
        $student = Student::findOne($id);
        $student->invoice = static::getStudentTotalInvoicesSum($id);
        $student->money = static::getStudentTotalPaymentsSum($id);
        $student->debt = $student->money - $student->invoice;
        if ($student->save()) {
            return true;
        } else {
            var_dump($student->getErrors());die();
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

    public static function getStudentOffices($id = 0)
    {
        $offices = (new \yii\db\Query())
        ->select(['id' => 'o.id', 'name' => 'o.name'])
        ->from(['so' => 'calc_student_office'])
        ->innerJoin('calc_office o', 'o.id=so.office_id')
        ->where(['so.student_id' => $id])
        ->all();
        return $offices;
    }

    /**
     *  метод переносит данные из профиля студента с id2 в профиль студента с id1
     */

    public static function mergeStudentAccounts($id1, $id2)
    {
        $result = [];
        if(Yii::$app->session->get('user.ustatus') == 3) {        
            /* main tables */
            $result['update_calls'] = Call::changeStudentId($id1, $id2);
            $result['update_invoices'] = Invoicestud::changeStudentId($id1, $id2);
            $result['update_payments'] = Moneystud::changeStudentId($id1, $id2);
            $result['update_sales'] = Salestud::changeStudentId($id1, $id2);
            $result['update_groups'] = Studgroup::changeStudentId($id1, $id2);
            $result['update_journals'] = Studjournalgroup::changeStudentId($id1, $id2);
            $result['update_journals_history'] = Studjournalgrouphistory::changeStudentId($id1, $id2);
            $result['update_messages'] = Message::changeStudentId($id1, $id2);
            /* optional tables */
            $result['update_studphones'] = Studphone::changeStudentId($id1, $id2);
            $result['update_logins_log'] = Studloginlog::changeStudentId($id1, $id2);
            $result['update_clientaccess'] = ClientAccess::mergeClientAccounts($id1, $id2);
            $result['update_sms_log'] = Smslog::changeStudentId($id1, $id2);
            $result['update_studname_history'] = Studnamehistory::changeStudentId($id1, $id2);
        }
        return $result;
    }    
}
