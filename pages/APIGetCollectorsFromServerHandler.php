<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../model/Collector.php';

use Analogous\Util\AuthHelper;
use Analogous\Repository\CollectorRepository;
use Analogous\Model\Collector;

use VeloFrame as WF;

# @route api/collectors/fromserver
class APIGetCollectorsFromServerHandler extends WF\DefaultPageController
{

    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();

        $server_id = $_GET['server_id'] ?? null;
        if (!$server_id) {
            return new WF\HTTPResponse("Missing server_id parameter", 400);
        }

        $crepo = new CollectorRepository();
        $collectors = $crepo->getCollectorsByServerId($server_id);
        if (!$collectors) {
            return new WF\HTTPResponse("No collectors found for server ID $server_id", 404);
        }

        return json_encode($collectors);
    }

}