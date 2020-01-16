<?php

namespace app\models\search;

use app\models\Book;
use app\models\Lang;
use app\models\Office;
use app\models\OfficeBook;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class OfficeBookSearch extends OfficeBook
{
    /** @var string */
    public $name;
    /** @var string */
    public $author;
    /** @var string */
    public $isbn;
    /** @var int */
    public $language;
    /** @var int */
    public $office;

    public function rules()
    {
        return [
            [['id', 'language', 'year', 'office'], 'integer'],
            [['name', 'author', 'isbn', 'status', 'comment'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(
            (new Book)->attributeLabels(),
            parent::attributeLabels(),
            [
                'language' => Yii::t('app', 'Language'),
                'office'   => Yii::t('app', 'Office'),
            ]
        );
    }

    public function search(array $params = []) : ActiveDataProvider
    {
        $obt = OfficeBook::tableName();
        $bt  = Book::tableName();
        $lt  = Lang::tableName();
        $ot  = Office::tableName();

        $query = (new yii\db\Query())
        ->select([
            'id'       => "{$obt}.id",
            'name'     => "{$bt}.name",
            'author'   => "{$bt}.author",
            'isbn'     => "{$bt}.isbn",
            'year'     => "{$obt}.year",
            'language' => "{$bt}.language_id",
            'office'   => "{$obt}.office_id",
            'status'   => "{$obt}.status",
            'comment'  => "{$obt}.comment",
        ])
        ->from($obt)
        ->innerJoin($bt, "{$bt}.id = {$obt}.book_id")
        ->innerJoin($ot, "{$ot}.id = {$obt}.office_id"); 

        $this->load($params);

        if ($this->validate()) {
            $query->andWhere(["{$obt}.visible" => 1]);
            $query->andFilterWhere(['like', "{$bt}.name", $this->name]);
            $query->andFilterWhere(['like', "{$bt}.isbn", $this->isbn]);
            $query->andFilterWhere(['like', "{$bt}.author", $this->author]);
            $query->andFilterWhere(["{$obt}.year" => $this->year]);
            $query->andFilterWhere(["{$obt}.status" => $this->status]);
            $query->andFilterWhere(["{$obt}.office_id" => $this->office]);
            $query->andFilterWhere(["{$bt}.language_id" => $this->language]);
            $query->andFilterWhere(['like', "{$obt}.comment", $this->comment]);
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
                    'isbn',
                    'year',
                    'language' => [
                        'asc' => ["{$lt}.name" => SORT_ASC],
                        'desc' => ["{$lt}.name" => SORT_DESC],
                    ],
                    'office' => [
                        'asc' => ["{$ot}.name" => SORT_ASC],
                        'desc' => ["{$ot}.name" => SORT_DESC],
                    ],
                    'status',
                    'comment',
                ],
                'defaultOrder' => [
                    'name' => SORT_DESC
                ],
            ],
        ]);
    }
}