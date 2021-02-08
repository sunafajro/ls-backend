<?php

namespace school\models\searches;

use school\models\UserTimeTracking;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Class UserTimeTrackingSearch
 * @package school\models\search
 *
 * @property string $type
 * @property string $start
 * @property string $end
 */
class UserTimeTrackingSearch extends UserTimeTracking
{
    /** @var string */
    public $type;
    /** @var string */
    public $start;
    /** @var string */
    public $end;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['type', 'start', 'end'], 'string'],
        ];
    }

    /**
     * @param ActiveQuery $query
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(ActiveQuery $query, array $params = []): ActiveDataProvider
    {
        $utt = UserTimeTracking::tableName();

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(["{$utt}.type" => $this->type]);
            $query->andFilterWhere(['like', "DATE_FORMAT({$utt}.start, '%d.%m.%Y %H:%i')", $this->start]);
            $query->andFilterWhere(['like', "DATE_FORMAT({$utt}.end, '%d.%m.%Y %H:%i')", $this->end]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'type',
                    'start',
                    'end',
                    'comment',
                ],
                'defaultOrder' => [
                    'start' => SORT_ASC,
                ],
            ],
        ]);
    }
}