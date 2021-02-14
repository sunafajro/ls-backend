<?php

namespace client\models\queries;

use client\models\MessageFile;
use yii\db\ActiveQuery;

/**
 * Class MessageFileQuery
 * @package client\models\queries
 *
 * @method MessageFile one($db = null)
 * @method MessageFile[] all($db = null)
 * @method MessageFileQuery|ActiveQuery byId(int $id)
 * @method MessageFileQuery|ActiveQuery byEntityId(int $id)
 * @method MessageFileQuery|ActiveQuery byEntityIds(array $id)
 * @method MessageFileQuery|ActiveQuery byUserId(int $id)
 */
class MessageFileQuery extends FileQuery
{

}