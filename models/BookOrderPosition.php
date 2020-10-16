<?php

namespace app\models;

use app\modules\school\models\User;
use Yii;
use yii\db\ActiveQuery;
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
 * @property BookOrderPositionItem[] $bookOrderPositionItems
 */

class BookOrderPosition extends ActiveRecord
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
            [['book_order_id', 'book_id', 'purchase_cost_id', 'selling_cost_id', 'count', 'office_id', 'user_id', 'visible'], 'integer'],
            [['paid'],       'number'],
            [['created_at'], 'safe'],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['visible'],    'default', 'value'=> 1],

            [['book_order_id', 'book_id', 'purchase_cost_id', 'selling_cost_id', 'count', 'paid', 'user_id'], 'required'],
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
            'office_id'        => Yii::t('app', 'Office'),
            'user_id'          => Yii::t('app', 'User'),
            'created_at'       => Yii::t('app', 'Created at'),
            'visible'          => Yii::t('app', 'Active'),
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

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(BookOrder::class, ['id' => 'book_order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOffice()
    {
        return $this->hasOne(Office::class, ['id' => 'office_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPurchaseCost()
    {
        return $this->hasOne(BookCost::class, ['id' => 'purchase_cost_id'])
        ->andOnCondition(['type' => BookCost::TYPE_PURCHASE]);
    }

    /**
     * @return ActiveQuery
     */
    public function getSellingCost()
    {
        return $this->hasOne(BookCost::class, ['id' => 'selling_cost_id'])
        ->andOnCondition(['type' => BookCost::TYPE_SELLING]);
    }

    /**
     * @return ActiveQuery
     */
    public function getBookOrderPositionItems()
    {
        return $this->hasMany(BookOrderPositionItem::class, ['book_order_position_id' => 'id']);
    }
}
