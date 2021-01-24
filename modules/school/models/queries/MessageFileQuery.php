<?php

namespace app\modules\school\models\queries;

use app\modules\school\models\MessageFile;
use yii\db\ActiveQuery;

/**
 * Class MessageFileQuery
 * @package app\modules\school\models\queries
 *
 * @method MessageFile one($db = null)
 * @method MessageFile[] all($db = null)
 * @method MessageFileQuery|ActiveQuery byId(int $id)
 * @method MessageFileQuery|ActiveQuery byEntityId(int $id)
 * @method MessageFileQuery|ActiveQuery byUserId(int $id)
 */
class MessageFileQuery extends FileQuery
{

}