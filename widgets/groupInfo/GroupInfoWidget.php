<?php

namespace app\widgets\groupInfo;

use app\models\Groupteacher;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class GroupInfoWidget extends Widget {
    /** @var Groupteacher|null  */
    public $group = null;

    /** {@inheritDoc} */
    public function run() {
        $data = [];
        if (!empty($this->group)) {
            $data['id']       = $this->group->id;
            $data['date']     = $this->group->data;
            $data['active']   = $this->group->visible === 1;
            $data['company']  = $this->group->company;
            if (($service = $this->group->service) !== null) {
                $data['serviceName'] = $service->name;
            }
            if (($level = $this->group->eduLevel) !== null) {
                $data['levelName'] = $level->name;
            }
            $data['teachers'] = Groupteacher::getGroupTeacherListString($this->group);
            if (($office = $this->group->office) !== null) {
                $data['officeName'] = $office->name;
            }
            $data['books'] = join(', ', ArrayHelper::getColumn($this->group->books ?? [], 'name'));
            $data['schedule'] = Groupteacher::getGroupLessonScheduleString($this->group->id);
            if (($timeNorm = $this->group->timeNorm) !== null) {
                $data['duration'] = $timeNorm->value;
            }
        }

        return $this->render('_groupInfo', [
            'data' => $data,
        ]);
    }
}