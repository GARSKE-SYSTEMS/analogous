<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../repository/ServerRepository.php';
require_once __DIR__ . '/../model/Collector.php';
require_once __DIR__ . '/../model/Server.php';
require_once __DIR__ . '/../util/CSRFHelper.php';

use Analogous\Util\AuthHelper;
use Analogous\Repository\CollectorRepository;
use Analogous\Repository\ServerRepository;
use Analogous\Model\Collector;
use Analogous\Model\Server;
use Analogous\Util\CSRFHelper;

use VeloFrame as WF;

# @route api/collectors/create
class APICreateCollectorHandler extends WF\DefaultPageController
{
    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();
        $csrfParam = $_GET['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($csrfParam)) {
            return new WF\HTTPResponse("Invalid CSRF token", 403);
        }

        $serverId = $_GET['server_id'] ?? null;
        $name = $_GET['name'] ?? null;

        if (!$serverId || !$name) {
            return new WF\HTTPResponse("Missing 'server_id' or 'name' parameter", 400);
        }

        $serverRepo = new ServerRepository();
        $server = $serverRepo->getServerById($serverId);
        if (!$server) {
            return new WF\HTTPResponse("Server not found with ID $serverId", 404);
        }

        $collector = new Collector(null, $server, $name);
        $collectorRepo = new CollectorRepository();
        $created = $collectorRepo->addCollector($collector);

        if (!$created) {
            return new WF\HTTPResponse("Failed to create collector", 500);
        }

        $resp = $created->toArray();
        $resp['csrf_token'] = CSRFHelper::generateToken();
        return json_encode($resp);
    }
}
