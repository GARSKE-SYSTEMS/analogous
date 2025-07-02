<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../util/CSRFHelper.php';
require_once __DIR__ . '/../repository/ServerRepository.php';
require_once __DIR__ . '/../model/Server.php';

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use Analogous\Repository\ServerRepository;
use Analogous\Model\Server;

use VeloFrame as WF;

# @route api/servers/create
class APICreateServerHandler extends WF\DefaultPageController
{
    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();
        $csrfParam = $_GET['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($csrfParam)) {
            return new WF\HTTPResponse("Invalid CSRF token", 403);
        }

        $name = $_GET['name'] ?? null;
        $ip = $_GET['ip'] ?? null;

        if (!$name || !$ip) {
            return new WF\HTTPResponse("Missing 'name' or 'ip' parameter", 400);
        }

        $server = new Server(null, $name, $ip);
        $repo = new ServerRepository();
        $created = $repo->addServer($server);

        if (!$created) {
            return new WF\HTTPResponse("Failed to create server", 500);
        }

        $resp = $created->toArray();
        $resp['csrf_token'] = CSRFHelper::generateToken();
        return json_encode($resp);
    }
}
