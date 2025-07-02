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

# @route api/servers
class APIGetServersHandler extends WF\DefaultPageController
{
    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();

        $csrfParam = $_GET['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($csrfParam)) {
            return new WF\HTTPResponse("Invalid CSRF token", 403);
        }

        $repo = new ServerRepository();
        $servers = $repo->getAllServers();
        if (!$servers) {
            $response = [
                'servers' => [],
                'csrf_token' => CSRFHelper::generateToken()
            ];
            return json_encode($response);
        }

        $ret = [];
        foreach ($servers as $server) {
            $ret[] = $server->toArray();
        }

        $response = ['servers' => $ret, 'csrf_token' => CSRFHelper::generateToken()];
        return json_encode($response);
    }
}
