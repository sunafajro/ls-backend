<?php

namespace console\controllers;

use school\models\AccessRuleAssignment;
use school\models\AccessRule;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Class AccessRuleController
 * @package console\controllers
 */
class AccessRuleController extends Controller
{
    public function actionDbToFile()
    {
        try {
            $rules = AccessRule::find()->asArray()->all();
            file_put_contents(Yii::getALias('@data/access_rules.json'), Json::encode($rules));
            Console::output("Access rules data wrote to the file.");
            $assignments = AccessRuleAssignment::find()->asArray()->all();
            file_put_contents(Yii::getALias('@data/access_rule_assignments.json'), Json::encode($assignments));
            Console::output("Access rule assignments data wrote to the file.");
        } catch (\Exception $e) {
            Console::output($e->getMessage());
        }
    }

    public function actionFileToDb()
    {
        try {
            AccessRule::deleteAll();
            AccessRuleAssignment::deleteAll();

            $file = file_get_contents(Yii::getALias('@data/access_rules.json'));
            $rules = Json::decode($file);
            foreach ($rules as $rule) {
                $model = new AccessRule();
                $model->load($rule, '');
                $model->save();
            }
            Console::output("Access rules data wrote to the db.");

            $file = file_get_contents(Yii::getALias('@data/access_rule_assignments.json'));
            $assignments = Json::decode($file);
            foreach ($assignments as $assignment) {
                $model = new AccessRuleAssignment();
                if (!empty($assignment['action'])) {
                    $assignment['access_rule_slug'] = "{$assignment['access_rule_slug']}_{$assignment['action']}";
                    unset($assignment['action']);
                }
                $model->load($assignment, '');
                if ($model->save()) {
                    if (($rule = AccessRule::find()->bySlug($model->access_rule_slug)->one()) === null) {
                        $rule = new AccessRule(['slug' => $model->access_rule_slug, 'name' => '<noname>']);
                        $rule->save();
                    }
                }
            }
            Console::output("Access rule assignments data wrote to the db.");
        } catch (\Exception $e) {
            Console::output($e->getMessage());
        }
    }
}