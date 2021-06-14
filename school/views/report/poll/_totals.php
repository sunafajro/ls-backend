<?php
/**
 * @var Poll $poll
 * @var array $totals
 */
use common\models\BasePoll as Poll;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$questions = $poll->getQuestions()->andWhere(['visible' => 1])->indexBy('id')->all();
?>
<div>
    <h3>Итого:</h3>
    <div>
        <?php
            foreach($questions as $questionId => $question) {
                $questionResponse = $totals[$questionId] ?? [];
                echo Html::beginTag('div');
                $questionItems = ArrayHelper::index($question->items ?? [], 'id');
                foreach($questionItems as $key => $questionItem) {
                    echo Html::beginTag('div');
                    echo $key . '. ' . $questionItem['title'] . ': ' . ($totals[$questionId][$key]['count'] ?? 0);
                    if (isset($questionItem['options']) && count($questionItem['options'])) {
                        echo Html::beginTag('div', ['style' => 'padding-left: 2rem']);
                        $questionSubItems = ArrayHelper::index($questionItem['options'] ?? [], 'id');
                        foreach($questionSubItems as $subKey => $subItem) {
                            echo Html::beginTag('div');
                            echo $subKey . '. ' . $subItem['title'] . ': ' . ($totals[$questionId][$key]['options'][$subKey] ?? 0);
                            echo Html::endTag('div');
                        }
                        echo Html::endTag('div');
                    }
                    echo Html::endTag('div');
                }
                echo Html::endTag('div');
            }
        ?>
    </div>
</div>