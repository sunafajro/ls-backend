<?php

namespace common\models;

/**
 * Class ActiveRecord
 * @package common\models;
 *
 * @property int $visible (0 - удален, 1 - действующий)
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @return bool
     */
    public function delete(): bool
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }
}