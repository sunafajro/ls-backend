<?php

namespace app\models;

use Yii;

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
 */

class BookOrderPosition extends \yii\db\ActiveRecord
{
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
            [['book_order_id', 'book_id', 'purchase_cost_id', 'selling_cost_id', 'count', 'paid'], 'required'],
            [['book_order_id', 'book_id', 'purchase_cost_id', 'selling_cost_id', 'count', 'office_id', 'user_id', 'visible'], 'integer'],
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
            'book_order_id'    => Yii::t('app', 'Book order ID'),
            'book_id'          => Yii::t('app', 'Book ID'),
            'purchase_cost_id' => Yii::t('app', 'Purchase cost ID'),
            'selling_cost_id'  => Yii::t('app', 'Selling cost ID'),
            'count'            => Yii::t('app', 'Count'),
            'paid'             => Yii::t('app', 'Paid'),
            'office_id'        => Yii::t('app', 'Office ID'),
            'user_id'          => Yii::t('app', 'User ID'),
            'created_at'       => Yii::t('app', 'Created at'),
            'visible'          => Yii::t('app', 'Active'),
        ];
    }

    public function restore()
    {
        $this->visible = 1;
        return $this->save();
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
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