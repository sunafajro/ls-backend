<?php

namespace school\models\searches;

use school\models\Book;
use school\models\BookCost;
use school\models\BookOrder;
use school\models\BookOrderPosition;
use yii\data\ActiveDataProvider;
use Yii;

class BookOrderSearch extends BookOrder
{
    /** @var int */
    public $total_book_count;
    /** @var float */
    public $total_purchase_cost;
    /** @var float */
    public $total_selling_cost;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'total_book_count'    => Yii::t('app', 'Books count'),
            'total_purchase_cost' => Yii::t('app', 'Purchase cost'),
            'total_selling_cost'  => Yii::t('app', 'Selling cost'),
        ]);
    }

    public function search(array $params = []) : ActiveDataProvider
    {
        $officeId = (int)Yii::$app->session->get('user.ustatus') === 4
            ? (int)Yii::$app->session->get('user.uoffice_id')
            : null;

        $bt   = Book::tableName();
        $bct  = BookCost::tableName();
        $bot  = BookOrder::tableName();
        $bopt = BookOrderPosition::tableName();

        $bookCountSubQuery = (new \yii\db\Query())
            ->select("SUM({$bopt}.count)")
            ->from($bopt)
            ->where("{$bopt}.book_order_id = {$bot}.id")
            ->andWhere(['visible' => 1])
            ->andFilterWhere(["{$bopt}.office_id" => $officeId]);

        $bookPurchaseCostSubQuery = (new \yii\db\Query())
        ->select("SUM({$bct}.cost * {$bopt}.count)")
        ->from($bopt)
        ->innerJoin($bt, "{$bt}.id = {$bopt}.book_id")
        ->innerJoin($bct, "{$bt}.id = {$bct}.book_id AND {$bct}.visible = :one AND {$bct}.type = :type", [':one' => 1, ':type' => BookCost::TYPE_PURCHASE])
        ->where("{$bopt}.book_order_id = {$bot}.id")
        ->andWhere(["{$bopt}.visible" => 1])
        ->andFilterWhere(["{$bopt}.office_id" => $officeId]);
        
        $bookSellingCostSubQuery = (new \yii\db\Query())
            ->select("SUM({$bopt}.paid)")
            ->from($bopt)
            ->where("{$bopt}.book_order_id = {$bot}.id")
            ->andWhere(["{$bopt}.visible" => 1])
            ->andFilterWhere(["{$bopt}.office_id" => $officeId]);

        $query = (new \yii\db\Query());
        $query->select([
            'id'                  => 'id',
            'date_start'          => 'date_start',
            'date_end'            => 'date_end',
            'total_book_count'    => $bookCountSubQuery,
            'total_purchase_cost' => $bookPurchaseCostSubQuery,
            'total_selling_cost'  => $bookSellingCostSubQuery,
            'status'              => 'status',
        ]);
        $query->from($bot);

        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(["{$bot}.visible" => 1]);
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
                    'date_start',
                    'date_end',
                    'status',
                ],
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);
    }
}