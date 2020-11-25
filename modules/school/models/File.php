<?php

namespace app\modules\school\models;

use app\modules\school\School;
use app\models\BaseFile;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "files".
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
 */

class File extends BaseFile
{
    const TYPE_DOCUMENTS    = 'documents';
    const TYPE_GROUP_FILES  = 'group_files';
    const TYPE_ATTACHMENTS  = 'attachments';
    const TYPE_CERTIFICATES = 'certificates';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['module_type'], 'default', 'value' => School::MODULE_NAME]
        ], parent::rules());
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return bool|string
     */
    public static function getTempDirPath()
    {
        $dirPathAlias = join('/', ['@files', School::MODULE_NAME, File::TYPE_TEMP]);

        return Yii::getAlias($dirPathAlias);
    }
}