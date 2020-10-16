<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "receipts".
 *
 * @property integer $id
 * @property string  $visible
 * @property string  $created_at
 * @property string  $user_id
 * @property string  $student_id
 * @property string  $purpose
 * @property string  $name
 * @property string  $payer
 * @property integer $sum
 * @property string  $qrdata
 */

class Receipt extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'receipts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['student_id', 'purpose', 'name', 'sum', 'qrdata'], 'required'],
            [['student_id', 'user_id', 'visible'], 'integer'],
            [['created_at', 'sum'], 'safe'],
            [['name', 'purpose', 'payer', 'qrdata'], 'string'],
            [['visible'],    'default', 'value' => 1],
            [['user_id'],    'default', 'value' => Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
        ];
    }

        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'       => Yii::t('app', 'Full name'),
            'purpose'    => Yii::t('app', 'Destination'),
            'sum'        => Yii::t('app', 'Sum'),
            'payer'      => Yii::t('app', 'Payer'),
            'user_id'    => Yii::t('app', 'User'),
            'created_at' => Yii::t('app', 'Created at'),
            'visible'    => Yii::t('app', 'Active'),
        ];
    }

    const RECEIPT_CODE = 'ST00012';
    const RECEIPT_LASTNAME = 'LASTNAME';
    const RECEIPT_PURPOSE = 'Purpose';
    const RECEIPT_SUM = 'Sum';
    const RECEIPT_COMPANY = [
        'key'   => 'Name',
        'title' => 'Наименование организации',
        'value' => 'ООО "Школа иностранных языков "Язык для Успеха"',
    ];
    const RECEIPT_PERSONAL_ACCOUNT = [
        'key'   => 'PersonalAcc',
        'title' => 'Расчетный счет',
        'value' => '40702810075000008515',
    ];
    const RECEIPT_BANK_NAME = [
        'key'   => 'BankName',
        'title' => 'Наименование банка',
        'value' => 'ЧУВАШСКОЕ ОТДЕЛЕНИЕ N8613 ПАО СБЕРБАНК'
    ];
    const RECEIPT_BIC = [
        'key'   => 'BIC',
        'title' => 'БИК',
        'value' => '049706609',
    ];
    const RECEIPT_CORRESPONDENT_ACCOUNT = [
        'key'   => 'CorrespAcc',
        'title' => 'Корреспондентский счет',
        'value' => '00000000000000000000',
    ];
    const RECEIPT_PAYER_INN = [
        'key'   => 'PayeeINN',
        'title' => 'ИНН',
        'value' => '2130122892',
    ];
    const RECEIPT_KPP = [
        'key'   => 'KPP',
        'title' => 'КПП',
        'value' => '213001001',
    ];

    public static function receiptFormParams()
    {
        return [
            self::RECEIPT_COMPANY,
            self::RECEIPT_PAYER_INN,
            self::RECEIPT_KPP,
            self::RECEIPT_BIC,
            self::RECEIPT_CORRESPONDENT_ACCOUNT,
            self::RECEIPT_BANK_NAME,
            self::RECEIPT_PERSONAL_ACCOUNT,
        ];
    }

    public static function receiptParamsStringified()
    {
        $params = [
            self::RECEIPT_CODE,
            self::RECEIPT_COMPANY['key']               . '=' . self::RECEIPT_COMPANY['value'],
            self::RECEIPT_PERSONAL_ACCOUNT['key']      . '=' . self::RECEIPT_PERSONAL_ACCOUNT['value'],
            self::RECEIPT_BANK_NAME['key']             . '=' . self::RECEIPT_BANK_NAME['value'],
            self::RECEIPT_BIC['key']                   . '=' . self::RECEIPT_BIC['value'],
            self::RECEIPT_CORRESPONDENT_ACCOUNT['key'] . '=' . self::RECEIPT_CORRESPONDENT_ACCOUNT['value'],
            self::RECEIPT_PAYER_INN['key']             . '=' . self::RECEIPT_PAYER_INN['value'],
            self::RECEIPT_KPP['key']                   . '=' . self::RECEIPT_KPP['value'],
            self::RECEIPT_COMPANY['key']               . '=' . self::RECEIPT_COMPANY['value'],
            self::RECEIPT_COMPANY['key']               . '=' . self::RECEIPT_COMPANY['value'],
        ];
        return implode('|', $params);
    }

    public function delete()
    {
        $this->visible = 0;
        
        return $this->save(true, ['visible']);
    }

    /**
     *  метод возвращает одну квитанцию по id
     */
    public function getReceipt(int $id) : array
    {
        $receipt = (new \yii\db\Query())
        ->select([
            'id'          => 'r.id',
            'date'        => 'r.created_at',
            'purpose'     => 'r.purpose',
            'name'        => 'r.name',
            'payer'       => 'r.payer',
            'sum'         => 'r.sum',
            'qrdata'      => 'r.qrdata',
            'studentId'   => 'r.student_id',
            'studentName' => 's.name',
        ])
        ->from(['r' => static::tableName()])
        ->innerJoin(['s' => Student::tableName()], 'r.student_id = s.id')
        ->where([
            'r.id' => $id,
            'r.visible' => 1,
        ])
        ->one();

        return $receipt;
    }

    /**
     *  метод возвращает список квитанций
     */
    public function getReceipts(int $sid) : ActiveDataProvider
    {
        $query = (new \yii\db\Query())
        ->select([
            'id'          => 'r.id',
            'date'        => 'r.created_at',
            'userName'    => 'u.name',
            'purpose'     => 'r.purpose',
            'name'        => 'r.name',
            'payer'       => 'r.payer',
            'sum'         => 'r.sum',
            'qrdata'      => 'r.qrdata',
            'studentId'   => 'r.student_id',
            'studentName' => 's.name',
        ])
        ->from(['r' => static::tableName()])
        ->innerJoin(['u' => 'user'], 'r.user_id = u.id')
        ->innerJoin(['s' => Student::tableName()], 'r.student_id = s.id')
        ->where([
            'r.student_id' => $sid,
            'r.visible' => 1,
        ]);
        
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
}
