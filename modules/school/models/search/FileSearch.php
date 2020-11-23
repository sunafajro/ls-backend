<?php


namespace app\modules\school\models\search;


use app\models\File;
use yii\data\ActiveDataProvider;

class FileSearch extends File
{
    public function search($query, $params) : ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @param int $groupId
     * @param array $params
     */
    public function searchByGroup(int $groupId, array $params = []) : ActiveDataProvider
    {
        $query = File::find()
            ->andWhere([
                'entity_type' => File::TYPE_GROUP_FILES,
                'entity_id'   => $groupId,
            ]);

        return $this->search($query, $params);
    }
}