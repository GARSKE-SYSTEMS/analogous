<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../util/CSRFHelper.php';
require_once __DIR__ . '/../repository/TokenRepository.php';

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use Analogous\Repository\TokenRepository;

use VeloFrame as WF;

# @route api/tokens/fromcollector
class APIGetTokensFromCollectorHandler extends WF\DefaultPageController
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

        $repo = new TokenRepository();
        $tokens = $repo->getTokensByCollectorId((int) $collectorId);
        if (empty($tokens)) {
            $response = [
                'tokens' => [],
                'csrf_token' => CSRFHelper::generateToken()
            ];
            return json_encode($response);
        }

        $out = [];
        foreach ($tokens as $token) {
            $out[] = $token->toArray();
        }

        $response = [
            'tokens' => $out,
            'csrf_token' => CSRFHelper::generateToken()
        ];

        return json_encode($response);
    }
}
