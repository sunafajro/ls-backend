<?php

namespace app\models;

use Yii;
use app\modules\school\models\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "files".
 *
 * @property integer $id
 * @property string  $file_name
 * @property string  $original_name
 * @property string  $entity_type
 * @property integer $entity_id
 * @property integer $user_id
 * @property string  $create_date
 */

class File extends ActiveRecord
{
    const TYPE_TEMP         = 'temp';
    const TYPE_USERS        = 'users';
    const TYPE_DOCUMENTS    = 'documents';
    const TYPE_GROUP_FILES  = 'group_files';
    const TYPE_ATTACHMENTS  = 'attachments';
    const TYPE_CERTIFICATES = 'certificates';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entity_type'], 'default', 'value' => self::TYPE_TEMP],
            [['user_id'], 'default', 'value' => Yii::$app->user->identity->id],
            [['create_date'], 'default', 'value' => date('Y-m-d')],
            [['file_name', 'original_name', 'entity_type'], 'string'],
            [['entity_id', 'user_id'], 'integer'],
            [['create_date'], 'safe'],
            [['file_name', 'original_name', 'entity_type', 'user_id', 'create_date'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'original_name' => Yii::t('app', 'File name'),
            'user_id'       => Yii::t('app', 'User'),
            'create_date'   => Yii::t('app', 'Date'),
        ];
    }

    /**
     * @return false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete()
    {
        return FileHelper::unlink($this->getPath()) ? parent::delete() : false;
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $filePath = [
            Yii::getAlias('@files'),
            $this->entity_type
        ];
        if ($this->entity_id) {
            $filePath[] = $this->entity_id;
        }
        $filePath[] = $this->file_name;

        return join('/', $filePath);
    }

    /**
     * @param string $entityType
     * @param int    $entityId
     * @return bool
     */
    public function setEntity(string $entityType, int $entityId = null)
    {
        $oldPath = $this->getPath();
        $newPath = [
            Yii::getAlias('@files'),
            $entityType,
        ];
        if ($entityId) {
            $newPath[] = $entityId;
        }
        $directory = join('/', $newPath);
        try {
            if (!file_exists($directory)) {
                FileHelper::createDirectory($directory);
            }
            $newPath[] = $this->file_name;
            if (rename($oldPath, join('/', $newPath))) {
                $this->entity_type = $entityType;
                if ($entityId) {
                    $this->entity_id = $entityId;
                }
                return $this->save(true, ['entity_type', 'entity_id']);
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}