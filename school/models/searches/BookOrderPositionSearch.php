<?php

namespace school\models\searches;

use school\models\Auth;
use school\models\Book;
use school\models\BookOrder;
use school\models\BookOrderPosition;
use school\models\Lang;
use school\models\Office;
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
    /** @var string */
    public $officeId;
    /** @var string */
    public $officeName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'isbn', 'author', 'description', 'publisher'], 'string'],
            [['language', 'officeName'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Book)->attributeLabels(), parent::attributeLabels(), [
            'language'      => Yii::t('app', 'Language'),
            'officeName'    => Yii::t('app', 'Office'),
            'purchase_cost' => Yii::t('app', 'Purchase cost'),
            'selling_cost'  => Yii::t('app', 'Selling cost'),
        ]);
    }

    public function search(BookOrder $bookOrder, array $params = []) : ActiveDataProvider
    {
        /** @var Auth $auth */
        $auth = Yii::$app->user->identity;

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
            'count'        => "SUM({$bopt}.count)",
            'paid'         => "SUM({$bopt}.paid)",
            'officeId'     => "GROUP_CONCAT({$ot}.id SEPARATOR ', ')",
            'officeName'   => "GROUP_CONCAT({$ot}.name SEPARATOR ', ')",
        ]);
        $query->from($bopt);
        $query->innerJoin($bt, "{$bt}.id = {$bopt}.book_id");
        $query->innerJoin($lt, "{$lt}.id = {$bt}.language_id");
        $query->innerJoin($ot, "{$ot}.id = {$bopt}.office_id");

        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(["{$bopt}.visible" => 1]);
            $query->andWhere(["{$bopt}.book_order_id" => $bookOrder->id]);
            $query->andFilterWhere(['like', "{$bt}.name", $this->name]);
            $query->andFilterWhere(["{$bt}.isbn" => $this->isbn]);
            $query->andFilterWhere(['like', "{$bt}.author", $this->author]);
            $query->andFilterWhere(['like', "{$bt}.publisher", $this->publisher]);
            $query->andFilterWhere(["{$bt}.language_id" => $this->language]);
            $query->andFilterWhere(["{$bopt}.office_id" => $this->officeName]);
        } else {
            $query->andWhere(new Expression("(0 = 1)"));
        }

        if ($auth->roleId === 4) {
            $query->andWhere(["{$bopt}.office_id" => $auth->officeId]);
        }

        $query->groupBy(["{$bopt}.id", "{$bt}.id"]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [

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
                    'officeName' => [
                        'asc' => ["officeName" => SORT_ASC],
                        'desc' => ["officeName" => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ],
            ],
        ]);
    }
}