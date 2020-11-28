<?php


namespace app\modules\school\models\search;

use app\modules\school\models\File;
use app\modules\school\School;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class FileSearch extends File
{
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['original_name'], 'string'],
        ];
    }

    /**
     * @param ActiveQuery $query
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(ActiveQuery $query, array $params) : ActiveDataProvider
    {
        $this->load($params);
        if ($this->validate()) {
            $query->andWhere(['like', 'lower(original_name)', mb_strtolower($this->original_name)]);
        } else {
            $query->andWhere('0 = 1');
        }

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