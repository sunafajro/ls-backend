<?php

namespace school\models;

use school\models\queries\FileQuery;
use school\School;
use common\models\BaseFile;
use yii\db\ActiveQuery;

/**
 * Class File
 * @package school\models
 *
 * @property integer $id
 * @property string  $file_name
 * @property string  $original_name
 * @property integer $size
 * @property string  $entity_type
 * @property integer $entity_id
 * @property string  $module_type
 * @property integer $user_id
 * @property string  $create_date
 *
 * @method static FileQuery|ActiveQuery find()
 */
class File extends BaseFile
{
    const TYPE_DOCUMENTS = 'documents';
    const TYPE_EXTERNAL_FILE_LINK = 'external_file_links';

    const DEFAULT_FIND_CLASS = FileQuery::class;
    const DEFAULT_MODULE_TYPE = School::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return array_merge([
            [['module_type'], 'default', 'value' => static::DEFAULT_MODULE_TYPE]
        ], parent::rules());
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}