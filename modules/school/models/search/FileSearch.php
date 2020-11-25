<?php


namespace app\modules\school\models\search;

use app\modules\school\models\File;
use app\modules\school\School;
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
                'module_type' => School::MODULE_NAME,
            ]);

        return $this->search($query, $params);
    }
}