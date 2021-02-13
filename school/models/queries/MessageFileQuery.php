<?php

namespace school\models\queries;

use school\models\MessageFile;
use yii\db\ActiveQuery;

/**
 * Class MessageFileQuery
 * @package school\models\queries
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