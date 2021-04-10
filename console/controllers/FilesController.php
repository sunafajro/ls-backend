<?php

namespace console\controllers;

use school\models\UserImage;
use school\models\User;
use school\School;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * Class FilesController
 * @package console\controllers
 */
class FilesController extends Controller
{
    public function actionMigrateUserImages()
    {
        $exclude = ['.', '..'];
        $pathSource = Yii::getAlias('@school/web/uploads/user');
        $pathDestination = Yii::getAlias('@data/files/school/user_image');
        if (!file_exists($pathDestination)) {
            FileHelper::createDirectory($pathDestination);
        }
        foreach(scandir($pathSource) as $dirName) {
            if (!in_array($dirName, $exclude)) {
                /** @var User|null $user */
                $user = User::find()->byId($dirName)->one();
                if (empty($user)) {
                    Console::output("Пользователь {$dirName} не найден. Пропуск каталога.");
                    continue;
                }
                $userDirPath = "{$pathSource}/{$dirName}";
                $files = FileHelper::findFiles($userDirPath);
                if (!empty($files)) {
                    $sourceImageFilePath = reset($files);
                    $fileNameArray = explode('\\', $sourceImageFilePath);
                    $fileName = array_pop($fileNameArray);
                    $name = explode('.', $fileName);
                    $newName = uniqid() . '.' . (array_pop($name));
                    $destinationImageFilePath = "{$pathDestination}/{$dirName}/{$newName}";
                    if (!file_exists("{$pathDestination}/{$dirName}")) {
                        FileHelper::createDirectory("{$pathDestination}/{$dirName}");
                    }
                    if (copy($sourceImageFilePath, $destinationImageFilePath)) {
                        (new Query())
                            ->createCommand()
                            ->insert(UserImage::tableName(), [
                                'file_name'     => $newName,
                                'original_name' => $fileName,
                                'entity_type'   => UserImage::TYPE_USER_IMAGE,
                                'entity_id'     => $dirName,
                                'user_id'       => 139,
                                'create_date'   => date('Y-m-d'),
                                'size'          => filesize($destinationImageFilePath),
                                'module_type'   => School::MODULE_NAME,
                            ])->execute();
                        $user->logo = '';
                        $user->save(true, ['logo']);
                    }
                }
            }
        }
    }
}
