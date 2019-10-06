<?php

namespace app\models\forms;

use app\models\Book;
use app\models\BookCost;
use Yii;
use yii\base\Model;

class BookForm extends Model {
    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $author;
    /** @var string */
    public $isbn;
    /** @var string */
    public $description;
    /** @var string */
    public $publisher;
    /** @var int */
    public $language_id;
    /** @var float */
    public $purchase_cost;
    /** @var float */
    public $selling_cost;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'author', 'isbn', 'publisher', 'language_id'], 'required'],
            [['id', 'language_id'], 'integer'],
            [['name', 'author', 'isbn', 'description', 'publisher'], 'string'],
            [['purchase_cost', 'selling_cost'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Book)->attributeLabels(), [
            'purchase_cost' => Yii::t('app', 'Purchase cost'),
            'selling_cost'  => Yii::t('app', 'Selling cost'),
        ]);
    }

    public function loadFromBook(Book $book)
    {
        $this->setAttributes($book->getAttributes());
        $this->purchase_cost = $book->purchaseCost->cost ?? 0.00;
        $this->selling_cost = $book->sellingCost->cost ?? 0.00;
    }

    public function save()
    {
        $book = !$this->id
            ? new Book()
            : Book::find()->andWhere(['id' => $this->id])->one();
        if (empty($book)) {
            $book = new Book();
        }
        $book->setAttributes($this->getAttributes());
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if(!$book->save()) {
                throw new \Exception('Не удалось добавить учебник.');
            }
            if (!$book->updateCost($this->purchase_cost, BookCost::TYPE_PURCHASE)) {
                throw new \Exception('Не удалось добавить учебник.');
            }
            if (!$book->updateCost($this->selling_cost, BookCost::TYPE_SELLING)) {
                throw new \Exception('Не удалось добавить учебник.');
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}