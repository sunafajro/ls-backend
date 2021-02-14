<?php

namespace school\models;

use school\models\queries\FileLinkQuery;
use yii\db\ActiveQuery;

/**
 * Class FileLink
 * @package school\models
 *
 * @method static FileLinkQuery|ActiveQuery find()
 */
class FileLink extends File
{
    const DEFAULT_ENTITY_TYPE = self::TYPE_EXTERNAL_FILE_LINK;
    const DEFAULT_FIND_CLASS = FileLinkQuery::class;

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        $attributes = parent::attributeLabels();
        $attributes['file_name'] = \Yii::t('app', 'Link');

        return $attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert && $this->entity_type === self::TYPE_TEMP) {
            $this->entity_type = self::DEFAULT_ENTITY_TYPE;
        }

        return true;
    }
}