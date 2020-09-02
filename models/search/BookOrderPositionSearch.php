<?php

namespace app\models\search;

use app\models\Book;
use app\models\BookOrder;
use app\models\BookOrderPosition;
use app\models\Lang;
use app\models\Office;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use Yii;
use yii\db\Query;

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
    /** @var int */
    public $office;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'isbn', 'author', 'description', 'publisher'], 'string'],
            [['language', 'office'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Book)->attributeLabels(), parent::attributeLabels(), [
            'language'      => Yii::t('app', 'Language'),
            'office'        => Yii::t('app', 'Office'),
            'purchase_cost' => Yii::t('app', 'Purchase cost'),
            'selling_cost'  => Yii::t('app', 'Selling cost'),
        ]);
    }

    public function search(BookOrder $bookOrder, array $params = []) : ActiveDataProvider
    {
        $bopt = BookOrderPosition::tableName();
        $bt   = Book::tableName();
        $lt   = Lang::tableName();
        $ot   = Office::tableName();

        $query = (new Query());
        $query->select([
            'id'           => "{$bopt}.id",
            'book_id'      => "{$bt}.id",
            'name'         => "{$bt}.name",
            'author'       => "{$bt}.author",
            'description'  => "{$bt}.description",
            'isbn'         => "{$bt}.isbn",
            'publisher'    => "{$bt}.publisher",
            'language'     => "{$bt}.language_id",
            'count'        => "{$bopt}.count",
            'paid'         => "{$bopt}.paid",
            'office'       => "{$bopt}.office_id",
        ]);
        $query->from($bopt);
        $query->innerJoin($bt, "{$bt}.id = {$bopt}.book_id");
        $query->innerJoin($lt, "{$lt}.id = {$bt}.language_id");

        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(["{$bopt}.visible" => 1]);
            $query->andWhere(["{$bopt}.book_order_id" => $bookOrder->id]);
            $query->andFilterWhere(['like', "{$bt}.name", $this->name]);
            $query->andFilterWhere(["{$bt}.isbn" => $this->isbn]);
            $query->andFilterWhere(['like', "{$bt}.author", $this->author]);
            $query->andFilterWhere(['like', "{$bt}.description", $this->description]);
            $query->andFilterWhere(['like', "{$bt}.publisher", $this->publisher]);
            $query->andFilterWhere(["{$bt}.language_id" => $this->language]);
            $query->andFilterWhere(["{$bopt}.office_id" => $this->office]);
        } else {
            $query->andWhere(new Expression("(0 = 1)"));
        }

        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
            $query->andWhere(["{$bopt}.office_id" => Yii::$app->session->get('user.uoffice_id')]);
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
                    'office' => [
                        'asc' => ["{$ot}.name" => SORT_ASC],
                        'desc' => ["{$ot}.name" => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
    }
}