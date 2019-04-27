<?php

namespace app\models;

use Yii;

use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "student_grades".
 *
 * @property integer $id
 * @property string $visible
 * @property string $date
 * @property string $user
 * @property string $studentId
 * @property string $purpose
 * @property string $name
 * @property integer $sum
 * @property string $qrdata
 */

class Receipt extends \yii\db\ActiveRecord
{
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
            [['user', 'studentId', 'purpose', 'name', 'sum', 'qrdata'], 'required'],
            [['studentId', 'user', 'visible'], 'integer'],
            [['date', 'sum'], 'safe'],
            [['name', 'purpose', 'qrdata'], 'string'],
        ];
    }

        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Full name'),
            'purpose' => Yii::t('app', 'Destination'),
            'sum' => Yii::t('app', 'Sum'),
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

    /**
     *  метод возвращает одну квитанцию по id
     */
    public function getReceipt(int $id) : array
    {
        $receipt = (new \yii\db\Query())
        ->select([
            'id' => 'r.id',
            'date' => 'r.date',
            'purpose' => 'r.purpose',
            'name' => 'r.name',
            'sum' => 'r.sum',
            'qrdata' => 'r.qrdata',
            'studentId' => 'r.studentId',
            'studentName' => 's.name',
        ])
        ->from(['r' => static::tableName()])
        ->innerJoin(['s' => 'calc_studname'], 'r.studentId = s.id')
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
            'id' => 'r.id',
            'date' => 'r.date',
            'userName' => 'u.name',
            'purpose' => 'r.purpose',
            'name' => 'r.name',
            'sum' => 'r.sum',
            'qrdata' => 'r.qrdata',
            'studentId' => 'r.studentId',
            'studentName' => 's.name',
        ])
        ->from(['r' => static::tableName()])
        ->innerJoin(['u' => 'user'], 'r.user = u.id')
        ->innerJoin(['s' => 'calc_studname'], 'r.studentId = s.id')
        ->where([
            'r.studentId' => $sid,
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