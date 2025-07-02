<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../repository/TokenRepository.php';
require_once __DIR__ . '/../service/CollectorService.php';
require_once __DIR__ . '/../model/Collector.php';
require_once __DIR__ . '/../model/Token.php';
require_once __DIR__ . '/../util/CSRFHelper.php';

use Analogous\Util\AuthHelper;
use Analogous\Repository\CollectorRepository;
use Analogous\Repository\TokenRepository;
use Analogous\Service\CollectorService;
use Analogous\Model\Collector;
use Analogous\Model\Token;
use Analogous\Util\CSRFHelper;

use VeloFrame as WF;

# @route api/tokens/create
class APICreateTokenHandler extends WF\DefaultPageController
{
    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();
        $csrfParam = $_GET['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($csrfParam)) {
            return new WF\HTTPResponse("Invalid CSRF token", 403);
        }

        $collectorId = $_GET['collector_id'] ?? null;
        if (!$collectorId) {
            return new WF\HTTPResponse("Missing 'collector_id' parameter", 400);
        }

        $collectorRepo = new CollectorRepository();
        $collector = $collectorRepo->getCollectorById($collectorId);
        if (!$collector) {
            return new WF\HTTPResponse("Collector not found with ID $collectorId", 404);
        }

        // generate a new token and persist it
        $token = CollectorService::createNewTokenFromCollector($collector);
        $tokenRepo = new TokenRepository();
        $created = $tokenRepo->create($token);

        if (!$created) {
            return new WF\HTTPResponse("Failed to create token", 500);
        }

        $resp = $created->toArray();
        $resp['csrf_token'] = CSRFHelper::generateToken();
        return json_encode($resp);
    }
}
