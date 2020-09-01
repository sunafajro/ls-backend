<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "book_order_positions".
 *
 * @property integer $id
 * @property integer $book_order_id
 * @property integer $book_id
 * @property integer $purchase_cost_id
 * @property integer $selling_cost_id
 * @property integer $count
 * @property float   $paid
 * @property string  $payment_type
 * @property string  $payment_comment
 * @property integer $office_id
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 * 
 * @property User      $user
 * @property Book      $book
 * @property BookOrder $bookOrder
 * @property BookCost  $purchaseCost
 * @property BookCost  $sellingCost
 * @property Office    $office
 */

class BookOrderPosition extends ActiveRecord
{
    const PAYMENT_TYPE_CASH = 'cash';
    const PAYMENT_TYPE_BANK = 'bank';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book_order_positions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['book_order_id', 'book_id', 'purchase_cost_id', 'selling_cost_id', 'count', 'paid', 'payment_type'], 'required'],
            [['book_order_id', 'book_id', 'purchase_cost_id', 'selling_cost_id', 'count', 'office_id', 'user_id', 'visible'], 'integer'],
            [['payment_type'], 'in', 'range' => [self::PAYMENT_TYPE_CASH, self::PAYMENT_TYPE_BANK]],
            [['payment_comment'], 'string'],
            [['paid'],       'number'],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['visible'],    'default', 'value'=> 1],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => '№',
            'book_order_id'    => Yii::t('app', 'Book order'),
            'book_id'          => Yii::t('app', 'Book ID'),
            'purchase_cost_id' => Yii::t('app', 'Purchase cost'),
            'selling_cost_id'  => Yii::t('app', 'Selling cost'),
            'count'            => Yii::t('app', 'Count'),
            'paid'             => Yii::t('app', 'Paid'),
            'payment_type'     => Yii::t('app', 'Payment type'),
            'payment_comment'  => Yii::t('app', 'Payment comment'),
            'office_id'        => Yii::t('app', 'Office'),
            'user_id'          => Yii::t('app', 'User'),
            'created_at'       => Yii::t('app', 'Created at'),
            'visible'          => Yii::t('app', 'Active'),
        ];
    }

    public function getPaymentTypes()
    {
        return [
            'cash' => Yii::t('app', 'Payment by cash'),
            'bank' => Yii::t('app', 'Payment by bank'),
        ];
    }

    public function restore()
    {
        $this->visible = 1;
        return $this->save(true, ['visible']);
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(BookOrder::class, ['id' => 'book_order_id']);
    }

    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }

    public function getPurchaseCost()
    {
        return $this->hasOne(BookCost::class, ['id' => 'purchase_cost_id'])
        ->andOnCondition(['type' => BookCost::TYPE_PURCHASE]);
    }
    public function getSellingCost()
    {
        return $this->hasOne(BookCost::class, ['id' => 'selling_cost_id'])
        ->andOnCondition(['type' => BookCost::TYPE_SELLING]);
    }
}