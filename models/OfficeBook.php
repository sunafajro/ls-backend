<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "office_books".
 *
 * @property integer $id
 * @property integer $book_id
 * @property integer $office_id
 * @property integer $year
 * @property integer $status
 * @property integer $comment
 * @property integer $visible
 * @property integer $user_id
 * @property string  $created_at
 * 
 * @property Book $book
 */
class OfficeBook extends \yii\db\ActiveRecord
{
    const STATUS_PRESENT = 'present';
    const STATUS_ISSUED  = 'issued';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'office_books';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year', 'comment'], 'trim'],
            [['visible'],    'default', 'value' => 1],
            [['user_id'],    'default', 'value' => Yii::$app->user->identity->id ?? 0],
            [['created_at'], 'default', 'value' => date('Y-m-d')],
            [['book_id', 'office_id', 'user_id', 'year'], 'integer'],
            [['comment'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_PRESENT, self::STATUS_ISSUED]],
            [['book_id', 'office_id', 'status', 'visible', 'user_id', 'created_at'], 'required'],
        ];
    }

    /**
     * Вместо физического удаления записи, меняет значение visible 1 => 0
     */
    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getStatuses() : array
    {
        return [
            self::STATUS_PRESENT => 'В наличии',
            self::STATUS_ISSUED  => 'Выдано',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'        => '№',
            'book_id'   => Yii::t('app', 'Book'),
            'office_id' => Yii::t('app', 'Office'),
            'comment'   => Yii::t('app', 'Comment'),
            'status'    => Yii::t('app', 'Status'),
            'year'      => Yii::t('app', 'Year'),
        ];
    }

    public function getBook()
    {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    public static function getBooksAutocomplete(string $term = NULL) : array
    {
        $whereClause = ['like', 'name', $term];
        // проверим возможно в запросе id, а не ФИО
        if (!preg_match( '/[^0-9]/', $term)) {
            $whereClause = ['id' => (int)$term];
        }
        return (new yii\db\Query())
            ->select(['label' => 'CONCAT("#", id, ", ", name, " ", author, ", ", isbn)', 'value' => 'id'])
            ->from(Book::tableName())
            ->where([
                'visible' => 1
            ])
            ->andFilterWhere($whereClause)
            ->limit(15)
            ->all();
    }
}