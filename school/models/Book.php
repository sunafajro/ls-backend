<?php

namespace school\models;

use school\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "books".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $author
 * @property string  $isbn
 * @property string  $description
 * @property string  $publisher
 * @property integer $language_id
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 * 
 * @property Lang       $language
 * @property BookCost   $purchaseCost
 * @property BookCost[] $purchaseCosts
 * @property BookCost   $sellingCost
 * @property BookCost[] $sellingCosts
 * @property User       $user
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'isbn', 'publisher', 'language_id'], 'required'],
            [['name', 'author', 'isbn', 'description', 'publisher'], 'string'],
            [['user_id', 'visible', 'language_id'], 'integer'],
            [['visible'],    'default', 'value'=> 1],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id ?? 0],
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
            'name'        => Yii::t('app', 'Name'),
            'author'      => Yii::t('app', 'Author'),
            'isbn'        => Yii::t('app', 'ISBN'),
            'description' => Yii::t('app', 'Description'),
            'publisher'   => Yii::t('app', 'Publisher'),
            'language_id' => Yii::t('app', 'Language'),
            'user_id'     => Yii::t('app', 'User'),
            'created_at'  => Yii::t('app', 'Created at'),
            'visible'     => Yii::t('app', 'Active'),
        ];
    }

    public function restore() : bool
    {
        $this->visible = 1;
        return $this->save(true, ['visible']);
    }

    public function delete() : bool
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public function getLanguage() : ActiveQuery
    {
        return $this->hasOne(Lang::class, ['id' => 'language_id']);
    }

    public function getUser() : ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPurchaseCosts() : ActiveQuery
    {
        return $this->hasMany(BookCost::class, ['book_id' => 'id'])
        ->andOnCondition([
            'type' => BookCost::TYPE_PURCHASE,
        ]);
    }

    public function getPurchaseCost() : BookCost
    {
        return $this->getPurchaseCosts()->andWhere(['visible' => 1])->one();
    }
   
    public function getSellingCosts() : ActiveQuery
    {
        return $this->hasMany(BookCost::class, ['book_id' => 'id'])
        ->andOnCondition([
            'type' => BookCost::TYPE_SELLING,
        ]);
    }


    public function getSellingCost() : BookCost
    {
        return $this->getSellingCosts()->andWhere(['visible' => 1])->one();
    }

    // Обновляет цену учебника
    public function updateCost($newCost, $type) : bool
    {
        if ($newCost !== '' && $newCost !== 0) {
            $cost = BookCost::find()->andWhere([
                'book_id' => $this->id,
                'cost'    => $newCost,
                'type'    => $type,
            ])->one();
            if (!empty($cost)) {
                if ($cost->visible) {
                    // Цена не изменилась
                    return true;
                } else {
                    // Новая цена уже была у данного учебника
                    $oldCost = $type === BookCost::TYPE_PURCHASE
                        ? ($this->purchaseCost ?? null)
                        : ($this->sellingCost ?? null);
                    if (!empty($oldCost)) {
                        if (!$oldCost->delete()) {
                            return false;
                        }
                    }
                    if (!$cost->restore()) {
                        return false;
                    }
                    return true;
                }
            } else {
                // Новая цена, ранее у данного учебника такой не было
                $oldCost = $type === BookCost::TYPE_PURCHASE
                    ? ($this->purchaseCost ?? null)
                    : ($this->sellingCost ?? null);
                $newBookCost = new BookCost();
                $newBookCost->book_id = $this->id;
                $newBookCost->cost = $newCost;
                $newBookCost->type = $type;
                if (!empty($oldCost)) {
                    if (!$oldCost->delete()) {
                        return false;
                    }
                }
                if (!$newBookCost->save()) {
                    var_dump($newBookCost->getErrors()); die();
                    return false;
                }

                return true;
            }
        } else {
            return true;
        }
    }
}
