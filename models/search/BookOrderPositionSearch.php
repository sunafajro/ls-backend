<?php

namespace app\models\search;

use app\models\Book;
use app\models\BookOrder;
use app\models\BookOrderPosition;
use app\models\Lang;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use Yii;

class BookOrderPositionSearch extends BookOrderPosition
{
    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $author;
    /** @var string */
    public $description;
    /** @var string */
    public $isbn;
    /** @var string */
    public $publisher;
    /** @var int */
    public $language;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'isbn', 'author', 'description', 'publisher'], 'string'],
            [['language'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Book)->attributeLabels(), parent::attributeLabels(), [
            'language'      => Yii::t('app', 'Language'),
            'purchase_cost' => Yii::t('app', 'Purchase cost'),
            'selling_cost'  => Yii::t('app', 'Selling cost'),
        ]);
    }

    public function search(BookOrder $bookOrder, array $params = []) : ActiveDataProvider
    {
        $bopt = BookOrderPosition::tableName();
        $bt = Book::tableName();
        $lt = Lang::tableName();

        $query = (new \yii\db\Query());
        $query->select([
            'id'          => "{$bt}.id",
            'name'        => "{$bt}.name",
            'author'      => "{$bt}.author",
            'description' => "{$bt}.description",
            'isbn'        => "{$bt}.isbn",
            'publisher'   => "{$bt}.publisher",
            'language'    => "{$bt}.language_id",
            'count'       => "{$bopt}.count",
            'paid'        => "{$bopt}.paid",
        ]);
        $query->from($bopt);
        $query->innerJoin($bt, "{$bt}.id = {$bopt}.book_id");
        $query->innerJoin($lt, "{$lt}.id = {$bt}.language_id");

        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(["{$bt}.visible" => 1]);
            $query->andWhere(["{$bopt}.book_order_id" => $bookOrder->id]);
            $query->andFilterWhere(['like', "{$bt}.name", $this->name]);
            $query->andFilterWhere(["{$bt}.isbn" => $this->isbn]);
            $query->andFilterWhere(['like', "{$bt}.author", $this->author]);
            $query->andFilterWhere(['like', "{$bt}.description", $this->description]);
            $query->andFilterWhere(['like', "{$bt}.publisher", $this->publisher]);
            $query->andFilterWhere(["{$bt}.language_id" => $this->language]);
        } else {
            $query->andWhere(new Expression("(0 = 1)"));
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'id',
                    'name',
                    'author',
                    'description',
                    'isbn',
                    'publisher',
                    'language' => [
                        'asc' => ["{$lt}.name" => SORT_ASC],
                        'desc' => ["{$lt}.name" => SORT_DESC],
                    ],
                    'count',
                    'paid',
                ],
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
    }
}