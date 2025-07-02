<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../util/CSRFHelper.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../model/Collector.php';

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use Analogous\Repository\CollectorRepository;
use Analogous\Model\Collector;

use VeloFrame as WF;

# @route api/collectors/fromserver
class APIGetCollectorsFromServerHandler extends WF\DefaultPageController
{

    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();
        $csrfParam = $_GET['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($csrfParam)) {
            return new WF\HTTPResponse("Invalid CSRF token", 403);
        }

        $server_id = $_GET['server_id'] ?? null;
        if (!$server_id) {
            return new WF\HTTPResponse("Missing server_id parameter", 400);
        }

        $crepo = new CollectorRepository();
        $collectors = $crepo->getCollectorsByServerId($server_id);
        if (!$collectors) {
            $response = [
                'collectors' => [],
                'csrf_token' => CSRFHelper::generateToken()
            ];
            return json_encode($response);
        }

        $ret = array();
        foreach ($collectors as $collector) {
            $ret[] = $collector->toArray();
        }
        $response = ['collectors' => $ret, 'csrf_token' => CSRFHelper::generateToken()];
        return json_encode($response);
    }

}