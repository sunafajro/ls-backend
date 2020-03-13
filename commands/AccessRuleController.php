<?php

namespace app\commands;

use app\models\AccessRule;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;

class AccessRuleController extends Controller {
    public function actionDbToFile()
    {
        try {
            $rules = AccessRule::find()->select(['controller' => 'controller', 'action' => 'action', 'role' => 'role'])->andWhere(['visible' => 1])->asArray()->all();
            file_put_contents(Yii::getALias('@app/data/access_rules.json'), Json::encode($rules));
            echo "Data wrote to the file.";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionFileToDb()
    {
        try {
            AccessRule::deleteAll();
            $file = file_get_contents(Yii::getALias('@app/data/access_rules.json'));
            $rules = Json::decode($file);
            foreach ($rules as $rule) {
                $model = new AccessRule();
                $model->load($rule, '');
                $model->save();
            }
            echo "Data wrote to the db.";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}