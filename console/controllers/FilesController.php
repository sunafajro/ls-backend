<?php


namespace app\commands;

use app\models\BaseFile;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\FileHelper;

class FilesController extends Controller
{
    public function actionMigrateDocuments()
    {
        $exclude = [
            '.', '..', '.gitkeep', BaseFile::TYPE_DOCUMENTS,
            BaseFile::TYPE_ATTACHMENTS, BaseFile::TYPE_TEMP,
            BaseFile::TYPE_CERTIFICATES, BaseFile::TYPE_USERS,
        ];
        $path  = Yii::getAlias('@files');
        if (!file_exists($path . '/' . BaseFile::TYPE_DOCUMENTS)) {
            FileHelper::createDirectory($path . '/' . BaseFile::TYPE_DOCUMENTS);
        }
        $files = scandir($path);
        foreach($files as $fileName) {
            if (!in_array($fileName, $exclude)) {
                $oldPath = "$path/{$fileName}";
                $name = explode('.', $fileName);
                $newName = uniqid() . '.' . (array_pop($name));
                $newPath = $path . '/' . BaseFile::TYPE_DOCUMENTS . '/' . $newName;
                if (rename($oldPath, $newPath)) {
                    $newFile = (new Query())
                        ->createCommand()
                        ->insert(BaseFile::tableName(), [
                            'file_name'     => $newName,
                            'original_name' => $fileName,
                            'entity_type'   => BaseFile::TYPE_DOCUMENTS,
                            'user_id'       => 139,
                            'create_date'   => date('Y-m-d'),
                        ])->execute();
                }
            }
        }
    }
}
