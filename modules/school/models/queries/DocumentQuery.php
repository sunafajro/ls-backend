<?php

namespace app\modules\school\models\queries;

use app\modules\school\models\Document;
use yii\db\ActiveQuery;

/**
 * Class DocumentQuery
 * @package app\modules\school\models\queries
 *
 * @method Document one($db = null)
 * @method Document[] all($db = null)
 * @method DocumentQuery|ActiveQuery byId(int $id)
 * @method DocumentQuery|ActiveQuery byEntityId(int $id)
 * @method DocumentQuery|ActiveQuery byUserId(int $id)
 */
class DocumentQuery extends FileQuery
{

}