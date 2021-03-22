<?php

namespace school\models\queries;

use common\models\queries\BaseActiveQuery;
use school\models\News;

/**
 * Class NewsQuery
 * @package school\models\queries
 *
 * @method News one($db = null)
 * @method News[] all($db = null)
 * @method NewsQuery byId(int $id)
 * @method NewsQuery byIds(array $ids)
 * @method NewsQuery active()
 * @method NewsQuery deleted()
 */
class NewsQuery extends BaseActiveQuery
{

}