<?php

namespace school\models\queries;

use common\models\queries\BaseFileQuery;
use school\models\File;
use yii\db\ActiveQuery;

/**
 * Class FileQuery
 * @package school\models\queries
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