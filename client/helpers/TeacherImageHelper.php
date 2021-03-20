<?php

namespace client\helpers;

use common\models\BaseFile;
use Yii;

/**
 * Class TeacherImageHelper
 * @package client\helpers
 */
class TeacherImageHelper
{
    const NO_PHOTO_PATH = 'images/no-photo.jpg';
    /**
     * @param int $id
     * @param string|null $oldPhoto
     *
     * @return string|null
     */
    public static function getImageWebPath(int $id, string $oldPhoto = null): ?string
    {
        $image = BaseFile::find()->byEntityId($id)->byEntityType(BaseFile::TYPE_USER_IMAGE)->one();
        if ($image !== null) {
            return "/files/download/{$image->id}";
        } else if (!empty($oldPhoto)) {
            $imagePath = "user/{$id}/logo/{$oldPhoto}";
            $image = Yii::getAlias("@uploads/{$imagePath}");
            if (file_exists($image)) {
                return Yii::getAlias("@web/uploads/{$imagePath}");
            }
        }

        return Yii::getAlias('@web/' . self::NO_PHOTO_PATH);
    }
}