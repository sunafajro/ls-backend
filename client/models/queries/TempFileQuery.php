<?php

namespace client\models\queries;

use client\models\TempFile;
use yii\db\ActiveQuery;

/**
 * Class TempFileQuery
 * @package client\models\queries
 *
 * @method TempFile one($db = null)
 * @method TempFile[] all($db = null)
 * @method TempFileQuery|ActiveQuery byId(int $id)
 * @method TempFileQuery|ActiveQuery byEntityId(int $id)
 * @method MessageFileQuery|ActiveQuery byEntityIds(array $id)
 * @method TempFileQuery|ActiveQuery byUserId(int $id)
 */
class TempFileQuery extends FileQuery
{

}