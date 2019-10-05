<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "book_costs".
 *
 * @property integer $id
 * @property integer $book_id
 * @property float   $cost
 * @property string  $type
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 * 
 * @property Book $book
 * @property User $user
 */
class BookCost extends \yii\db\ActiveRecord
{
    const TYPE_PURCHASE = 'purchase';
    const TYPE_SELLING  = 'selling';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'book_costs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cost', 'book_id', 'type'], 'required'],
            [['type'], 'string'],
            [['cost'], 'number'],
            [['user_id', 'visible', 'book_id'], 'integer'],
            [['visible'],    'default', 'value'=> 1],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => '№',
            'book_id'     => Yii::t('app', 'Book ID'),
            'type'        => Yii::t('app', 'Cost type'),
            'user_id'     => Yii::t('app', 'User ID'),
            'created_at'  => Yii::t('app', 'Created at'),
            'visible'     => Yii::t('app', 'Active'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
    }

    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_PURCHASE => Yii::t('app', 'Purchase cost'),
            self::TYPE_SELLING => Yii::t('app', 'Selling cost'),
        ];
    }

    public static function getTypeLabel(string $key) : string
    {
        $statuses = self::getTypeLabels();
        return $statuses[key] ?? '';
    }

    public function getBook()
    {
        $this->hasOne(Book::class, ['id', 'book_id']);
    }

    public function getUser()
    {
        $this->hasOne(User::class, ['id', 'user_id']);
    }
}
