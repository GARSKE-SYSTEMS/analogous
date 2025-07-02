<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../repository/LineRepository.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../model/Line.php';
require_once __DIR__ . '/../util/CSRFHelper.php';

use Analogous\Util\AuthHelper;
use Analogous\Repository\LineRepository;
use Analogous\Repository\CollectorRepository;
use Analogous\Model\Line;
use VeloFrame as WF;
use Analogous\Util\CSRFHelper;

# @route dataingest
class DataIngestHandler extends WF\DefaultPageController
{

    public function handlePost(array $params)
    {
        $token = AuthHelper::requireTokenAuth();
        if (!$token) {
            return new WF\HTTPResponse("Missing Auth Token", 401);
        }

        $body = file_get_contents('php://input');
        if (!$body) {
            return new WF\HTTPResponse("No data provided", 400);
        }

        // Line by line (for every line break)
        $lines = explode("\n", $body);
        if (count($lines) === 0) {
            return new WF\HTTPResponse("No data provided", 400);
        }

        // Invert array to process from oldest to newest
        $lines = array_reverse($lines);

        foreach ($lines as $body) {
            $line = new Line(null, $token->getCollector(), $body);
            $lineRepo = new LineRepository();
            $addedLine = $lineRepo->addLine($line);
            if (!$addedLine) {
                return new WF\HTTPResponse("Failed to add line", 500);
            }
        }
        // return new CSRF token for next request
        $newCsrf = CSRFHelper::generateToken();
        return new WF\HTTPResponse(json_encode(['csrf_token' => $newCsrf]), 200);

    }

}