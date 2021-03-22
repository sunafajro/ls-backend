<?php

namespace school\models\queries;

use common\models\queries\BaseActiveQuery;
use school\models\City;

/**
 * Class CityQuery
 * @package school\models\queries
 *
 * @method City one($db = null)
 * @method City[] all($db = null)
 * @method CityQuery byId(int $id)
 * @method CityQuery byIds(array $id)
 * @method CityQuery active()
 * @method CityQuery deleted()
 */
class CityQuery extends BaseActiveQuery
{

}