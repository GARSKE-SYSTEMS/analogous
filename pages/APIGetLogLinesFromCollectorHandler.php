<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../util/CSRFHelper.php';
require_once __DIR__ . '/../repository/LineRepository.php';
require_once __DIR__ . '/../model/Line.php';

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use Analogous\Repository\LineRepository;
use Analogous\Model\Line;

use VeloFrame as WF;

# @route api/loglines/fromcollector
class APIGetLogLinesFromCollectorHandler extends WF\DefaultPageController
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

        // pagination parameters
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 100;

        $repo = new LineRepository();
        $lines = $repo->findByCollectorId($collectorId, $offset, $limit);
        // get total count of log lines for this collector
        $total = $repo->countByCollectorId((int) $collectorId);
        if (empty($lines)) {
            $response = [
                'loglines' => [],
                'total' => $total,
                'csrf_token' => CSRFHelper::generateToken()
            ];
            return json_encode($response);
        }

        $out = [];
        foreach ($lines as $line) {
            $out[] = $line->toArray();
        }

        $response = [
            'loglines' => $out,
            'total' => $total,
            'csrf_token' => CSRFHelper::generateToken()
        ];

        return json_encode($response);
    }
}
