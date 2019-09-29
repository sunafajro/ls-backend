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
            [['name', 'isbn'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'publisher' => Yii::t('app', 'Publisher'),
            'language' => Yii::t('app', 'Language'),
        ]);
    }

    public function search(array $params = []) : ActiveDataProvider
    {
        $bt = Book::tableName();
        $bpt = 'calc_bookpublisher';
        $lt = Lang::tableName();

        $query = (new \yii\db\Query());
        $query->select([
            'id'          => "{$bt}.id",
            'name'        => "{$bt}.name",
            'author'      => "{$bt}.author",
            'description' => "{$bt}.description",
            'isbn'        => "{$bt}.isbn",
            'publisher'   => "{$bpt}.name",
            'language'    => "{$lt}.name",
        ]);
        $query->from($bt);
        $query->innerJoin($bpt, "{$bpt}.id = {$bt}.calc_bookpublisher");
        $query->innerJoin($lt, "{$lt}.id = {$bt}.calc_lang");

        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(["{$bt}.visible" => 1]);
            $query->andFilterWhere(["{$bt}.name" => $this->name]);
            $query->andFilterWhere(["{$bt}.isbn" => $this->isbn]);
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
                    'publisher' => [
                        'asc' => ["{$bpt}.name" => SORT_ASC],
                        'desc' => ["{$bpt}.name" => SORT_DESC],
                    ],
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