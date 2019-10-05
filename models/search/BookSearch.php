<?php


namespace app\models\search;

use app\models\Book;
use app\models\Lang;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use Yii;

class BookSearch extends Book
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
    /** @var string */
    public $language;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'isbn', 'author', 'publisher'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'language' => Yii::t('app', 'Language'),
        ]);
    }

    public function search(array $params = []) : ActiveDataProvider
    {
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
            'language'    => "{$lt}.name",
        ]);
        $query->from($bt);
        $query->innerJoin($lt, "{$lt}.id = {$bt}.language_id");

        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(["{$bt}.visible" => 1]);
            $query->andFilterWhere(['like', "{$bt}.name", $this->name]);
            $query->andFilterWhere(["{$bt}.isbn" => $this->isbn]);
            $query->andFilterWhere(['like', "{$bt}.author", $this->author]);
            $query->andFilterWhere(['like', "{$bt}.publisher", $this->publisher]);
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
                ],
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
    }
}