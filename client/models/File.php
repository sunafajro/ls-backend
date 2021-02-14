<?php

namespace client\models;

use client\models\queries\FileQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

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
class File extends ActiveRecord
{
    const DEFAULT_FIND_CLASS = FileQuery::class;
    const DEFAULT_MODULE_TYPE = 'school';
    const DEFAULT_ENTITY_TYPE = null;

    const TYPE_TEMP          = 'temp';
    const TYPE_USERS         = 'users';
    const TYPE_GROUP_FILES   = 'group_files';
    const TYPE_MESSAGE_FILES = 'message_files';
    const TYPE_MESSAGE_IMAGE = 'message_image';
    const TYPE_CERTIFICATES  = 'certificates';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'files';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['entity_type'], 'default', 'value' => self::TYPE_TEMP],
            [['module_type'], 'default', 'value' => static::DEFAULT_MODULE_TYPE],
            [['user_id'], 'default', 'value' => Yii::$app->user->identity->id],
            [['create_date'], 'default', 'value' => date('Y-m-d')],
            [['file_name', 'original_name', 'entity_type', 'module_type'], 'string'],
            [['size', 'entity_id', 'user_id'], 'integer'],
            [['create_date'], 'safe'],
            [['size', 'file_name', 'original_name', 'entity_type', 'user_id', 'create_date'], 'required'],
        ];
    }

    /**
     * @return FileQuery|ActiveQuery
     */
    public static function find(): ActiveQuery
    {
        $findClass = static::DEFAULT_FIND_CLASS;
        $findCondition = static::getDefaultFindCondition();
        $findQuery = new $findClass(get_called_class(), []);

        return $findQuery->andFilterWhere($findCondition);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'original_name' => Yii::t('app', 'File name'),
            'size' => Yii::t('app', 'Size'),
            'user_id' => Yii::t('app', 'User ID'),
            'create_date' => Yii::t('app', 'Upload date'),
        ];
    }

    public function delete()
    {
        return FileHelper::unlink($this->getPath()) ? parent::delete() : false;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        $filePath = [
            Yii::getAlias('@files'),
            $this->module_type,
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
    public function setEntity(string $entityType, int $entityId = null): bool
    {
        $oldPath = $this->getPath();
        $newPath = [
            Yii::getAlias('@files'),
            $this->module_type,
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

    /**
     * @return array
     */
    public static function getDefaultFindCondition(): array
    {
        $condition = [];
        $tb = static::tableName();
        if (static::DEFAULT_MODULE_TYPE) {
            $condition["{$tb}.module_type"] = static::DEFAULT_MODULE_TYPE;
        }
        if (static::DEFAULT_ENTITY_TYPE) {
            $condition["{$tb}.entity_type"] = static::DEFAULT_ENTITY_TYPE;
        }
        return $condition;
    }

    /**
     * @return bool|string
     */
    public static function getTempDirPath()
    {
        $dirPathAlias = join('/', ['@files', 'school', File::TYPE_TEMP]);

        return Yii::getAlias($dirPathAlias);
    }
}