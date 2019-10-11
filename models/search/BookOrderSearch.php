<?php

namespace app\models\search;

use app\models\BookOrder;
use app\models\BookOrderPosition;
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

        $bot = BookOrder::tableName();
        $bopt = BookOrderPosition::tableName();

        $bookCountSubQuery = (new \yii\db\Query())
            ->select("SUM({$bopt}.count)")
            ->from($bopt)
            ->where("{$bopt}.book_order_id = {$bot}.id")
            ->andWhere(['visible' => 1])
            ->andFilterWhere(["{$bopt}.office_id" => $officeId]);

        $bookCostSubQuery = (new \yii\db\Query())
            ->select("SUM({$bopt}.paid)")
            ->from($bopt)
            ->where("{$bopt}.book_order_id = {$bot}.id")
            ->andWhere(['visible' => 1])
            ->andFilterWhere(["{$bopt}.office_id" => $officeId]);

        $query = (new \yii\db\Query());
        $query->select([
            'id'                 => 'id',
            'date_start'         => 'date_start',
            'date_end'           => 'date_end',
            'total_book_count'   => $bookCountSubQuery,
            'total_selling_cost' => $bookCostSubQuery,
            'status'             => 'status',
        ]);
        $query->from($bot);

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