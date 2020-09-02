<?php


namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "book_order_position_items".
 *
 * @property integer $id
 * @property integer $book_order_position_id
 * @property string  $student_name
 * @property integer $student_id
 * @property integer $count
 * @property float   $paid
 * @property string  $payment_type
 * @property string  $payment_comment
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 *
 * @property BookOrderPosition $bookOrderPosition
 * @property Student $student
 * @property User    $user
 */
class BookOrderPositionItem extends ActiveRecord
{
    const PAYMENT_TYPE_CASH = 'cash';
    const PAYMENT_TYPE_BANK = 'bank';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book_order_position_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['book_order_position_id', 'student_id', 'count'], 'integer'],
            [['payment_type'], 'in', 'range' => [self::PAYMENT_TYPE_CASH, self::PAYMENT_TYPE_BANK]],
            [['student_name', 'payment_comment'], 'string'],
            [['paid'],       'number'],
            [['created_at'], 'safe'],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['visible'],    'default', 'value'=> 1],
            [['book_order_position_id', 'user_id', 'count', 'paid', 'payment_type'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => '№',
            'book_order_position_id' => Yii::t('app', 'Book order position ID'),
            'student_name'           => Yii::t('app', 'Student name'),
            'student_id'             => Yii::t('app', 'Student ID'),
            'count'                  => Yii::t('app', 'Count'),
            'paid'                   => Yii::t('app', 'Paid'),
            'payment_type'           => Yii::t('app', 'Payment type'),
            'payment_comment'        => Yii::t('app', 'Payment comment'),
            'user_id'                => Yii::t('app', 'User'),
            'created_at'             => Yii::t('app', 'Created at'),
            'visible'                => Yii::t('app', 'Active'),
        ];
    }

    /**
     * @return array
     */
    public function getPaymentTypes()
    {
        return [
            'cash' => Yii::t('app', 'Payment by cash'),
            'bank' => Yii::t('app', 'Payment by bank'),
        ];
    }

    /**
     * @return bool
     */
    public function restore()
    {
        $this->visible = 1;
        return $this->save(true, ['visible']);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBookOrderPosition()
    {
        return $this->hasOne(BookOrderPosition::class, ['id' => 'book_order_position_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}