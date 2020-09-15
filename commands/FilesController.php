<?php


namespace app\commands;

use app\models\File;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\FileHelper;

class FilesController extends Controller
{
    public function actionMigrateDocuments()
    {
        $exclude = [
            '.', '..', '.gitkeep', File::TYPE_DOCUMENTS,
            File::TYPE_ATTACHMENTS, File::TYPE_TEMP,
            File::TYPE_CERTIFICATES, File::TYPE_USERS,
        ];
        $path  = Yii::getAlias('@files');
        if (!file_exists($path . '/' . File::TYPE_DOCUMENTS)) {
            FileHelper::createDirectory($path . '/' . File::TYPE_DOCUMENTS);
        }
        $files = scandir($path);
        foreach($files as $fileName) {
            if (!in_array($fileName, $exclude)) {
                $oldPath = "$path/{$fileName}";
                $name = explode('.', $fileName);
                $newName = uniqid() . '.' . (array_pop($name));
                $newPath = $path . '/' . File::TYPE_DOCUMENTS . '/' . $newName;
                if (rename($oldPath, $newPath)) {
                    $newFile = (new Query())
                        ->createCommand()
                        ->insert(File::tableName(), [
                            'file_name'     => $newName,
                            'original_name' => $fileName,
                            'entity_type'   => File::TYPE_DOCUMENTS,
                            'user_id'       => 139,
                            'create_date'   => date('Y-m-d'),
                        ])->execute();
                }
            }
        }
    }
}
