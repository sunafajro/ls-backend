<?php

namespace common\models\searches;

use common\models\BaseFile;
use yii\data\ActiveDataProvider;

/**
 * Class BaseFileSearch
 * @package common\models\searches
 */
class BaseFileSearch extends BaseFile
{
    const ENTITY_CLASS = BaseFile::class;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['original_name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @param int|null $entityId
     *
     * @return ActiveDataProvider
     */
    public function search(array $params, int $entityId = null): ActiveDataProvider
    {
        $query = call_user_func([static::ENTITY_CLASS, 'find']);
        if ($entityId) {
            $query->byEntityId($entityId);
        }
        $this->load($params);
        if ($this->validate()) {
            $query->andWhere([
                'like',
                'lower(original_name)',
                mb_strtolower($this->original_name)
            ]);
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
                    'original_name',
                    'size',
                    'create_date',
                ],
                'defaultOrder' => [
                    'original_name' => SORT_ASC,
                ],
            ],
        ]);
    }
}