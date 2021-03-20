<?php

namespace common\models\forms;

use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 *
 * Class UploadForm
 * @package common\models\forms
 *
 * @property UploadedFile $file
 * @property string       $file_name
 * @property string       $original_name
 */
class BaseUploadForm extends Model
{
    const MAX_RESOLUTION = 800;

    /** @var UploadedFile */
    public $file;
    /** @var string */
    public $file_name;
    /** @var string */
    public $original_name;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @param string $path
     * @param bool $resizeImage
     *
     * @return bool
     * @throws Exception
     */
    public function saveFile(string $path, bool $resizeImage = false): bool
    {
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $this->original_name = $this->file->name;
        $this->file_name = uniqid() . '.' . $this->file->extension;

        $fullFilePath = "{$path}/{$this->file_name}";
        if ($this->file->saveAs($fullFilePath)) {
            if ($resizeImage && exif_imagetype($fullFilePath)) {
                $maxResolutionSize = \Yii::$app->params['upload.image.maxResolution'] ?? self::MAX_RESOLUTION;
                $imagine = Image::getImagine()->open($fullFilePath);
                $currentSize = $imagine->getSize();
                $currentWidth = $currentSize->getWidth();
                $currentHeight = $currentSize->getHeight();
                if ($currentWidth > $maxResolutionSize || $currentHeight > $maxResolutionSize) {
                    if ($currentWidth >= $currentHeight) {
                        Image::resize($fullFilePath, $maxResolutionSize, NULL)->save($fullFilePath);
                    } else {
                        Image::resize($fullFilePath, NULL, $maxResolutionSize)->save($fullFilePath);
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $spath
     * @param string $id
     * @param string $subdir
     * @return string
     * @throws Exception
     */
    public function resizeAndSave(string $spath, string $id, string $subdir) : string
    {
        //задаем адрес папки для загрузки файла
        $filepath = $spath . '/' . $id . '/' . $subdir . '/';
        //задаем имя файла
        $filename = "file-" . $id . "." . $this->file->extension;
        //проверяем наличие папки
        if (!file_exists($filepath)) {
            FileHelper::createDirectory($filepath);
        }
        $fullFilePath = $filepath.$filename;
        $this->file->saveAs($fullFilePath);
        if (exif_imagetype($fullFilePath)) {
            // уменьшаем изображение до 300px по ширине или высоте
            $imagine = Image::getImagine()->open($fullFilePath);
            $currentSize = $imagine->getSize();
            if ($currentSize->getWidth() >= $currentSize->getHeight()) {
                Image::resize($fullFilePath, 300, NULL)->save($fullFilePath);
            } else {
                Image::resize($fullFilePath, NULL, 300)->save($fullFilePath);
            }
        }

        return $filename;
    }
}