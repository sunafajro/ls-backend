<?php

namespace client\models\searches;

use client\models\File;
use yii\data\ActiveDataProvider;

/**
 * Class FileSearch
 * @package client\models\search
 */
class FileSearch extends File
{
    const ENTITY_CLASS = File::class;

    /** @var int|int[] */
    public $entityId;

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
    public function search(array $params): ActiveDataProvider
    {
        $query = call_user_func([static::ENTITY_CLASS, 'find']);
        if (!empty($this->entityId)) {
            if (is_array($this->entityId)) {
                $query->byEntityIds($this->entityId);
            } else {
                $query->byEntityId($this->entityId);
            }
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