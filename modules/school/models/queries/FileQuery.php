<?php

namespace app\modules\school\models\queries;

use app\models\queries\BaseFileQuery;
use app\modules\school\models\File;
use yii\db\ActiveQuery;

/**
 * Class FileQuery
 * @package app\modules\school\models\queries
 *
 * @method File one($db = null)
 * @method File[] all($db = null)
 * @method FileQuery|ActiveQuery byId(int $id)
 * @method FileQuery|ActiveQuery byEntityId(int $id)
 * @method FileQuery|ActiveQuery byUserId(int $id)
 */
class FileQuery extends BaseFileQuery
{

}