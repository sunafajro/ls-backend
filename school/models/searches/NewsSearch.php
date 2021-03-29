<?php

namespace school\models\searches;

use school\models\News;
use school\models\User;
use yii\data\ActiveDataProvider;

/**
 * Class NewsSearch
 * @package school\models\searches
 */
class NewsSearch extends News
{
    public $startDate;
    public $endDate;
    public $userName;

    public function rules(): array
    {
        return [
            [['startDate', 'endDate'], 'string'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $nt = News::tableName();
        $query = static::find()
            ->select("{$nt}.*")
            ->addSelect(['userName' => 'u.name'])
            ->innerJoin(['u' => User::tableName()], "{$nt}.author = u.id")
            ->active()
            ->orderBy(["{$nt}.date" => SORT_DESC]);

        $this->load($params);
        if ($this->validate()) {
            $startDate = \DateTime::createFromFormat('d.m.Y', $this->startDate);
            $endDate = \DateTime::createFromFormat('d.m.Y', $this->endDate);
            if ($startDate) {
                $query->andFilterWhere(['>=', "{$nt}.date", $startDate->format('Y-m-d')]);
            } else {
                $this->startDate = null;
            }
            if ($endDate) {
                $query->andFilterWhere(['<=', "{$nt}.date", $endDate->format('Y-m-d')]);
            } else {
                $this->endDate = null;
            }
        } else {
            $query->where("0 = 1");
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
    }
}