<?php
namespace app\models;

use yii\base\Model;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function saveFile(string $path)
    {
        $filesearch = FileHelper::findFiles($path, ['only' => [$this->file->name]]);
        if (empty($filesearch)) {
            FileHelper::createDirectory($path);
        }
        return $this->file->saveAs($path . $this->file->name);
    }

    public function resizeAndSave(string $spath, string $id, string $subdir) : string
    {
        //задаем адрес папки для загрузки файла
        $filepath = $spath . '/' . $id . '/' . $subdir . '/';
        //задаем имя файла
        $filename = "file-" . $id . "." . $this->file->extension;
        //проверяем наличие файла и папки
        $filesearch = FileHelper::findFiles($spath, ['only' => [$filename]]);
        if (empty($filesearch)) {
            FileHelper::createDirectory($spath . '/' . $id . '/');
            FileHelper::createDirectory($filepath);
        }
        $fullFilePath = $filepath.$filename;
        $this->file->saveAs($fullFilePath);
        // уменьшаем изображение до 300px по ширине или высоте
        $imagine = Image::getImagine()->open($fullFilePath);
        $currentSize = $imagine->getSize();
        if ($currentSize->getWidth() >= $currentSize->getHeight()) {
            Image::resize($fullFilePath, 300, NULL)->save($fullFilePath);
        } else {
            Image::resize($fullFilePath, NULL, 300)->save($fullFilePath);
        }
        return $filename;
    }
}
?>