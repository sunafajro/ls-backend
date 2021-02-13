<?php
namespace school\models\forms;

use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 *
 * @property UploadedFile $file
 * @property string       $file_name
 * @property string       $original_name
 */
class UploadForm extends Model
{
    /** @var UploadedFile */
    public $file;
    /** @var string */
    public $file_name;
    /** @var string */
    public $original_name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }

    /**
     * @param string $path
     * @return bool
     * @throws Exception
     */
    public function saveFile(string $path)
    {
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $this->original_name = $this->file->name;
        $this->file_name = uniqid() . '.' . $this->file->extension;

        return $this->file->saveAs("{$path}/{$this->file_name}");
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
?>