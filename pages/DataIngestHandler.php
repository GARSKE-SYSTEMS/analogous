<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../repository/LineRepository.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../model/Line.php';

use Analogous\Util\AuthHelper;
use Analogous\Repository\LineRepository;
use Analogous\Repository\CollectorRepository;
use Analogous\Model\Line;
use VeloFrame as WF;

# @route dataingest
class DataIngestHandler extends WF\DefaultPageController
{

    public function handleGet(array $params)
    {
        $token = AuthHelper::requireTokenAuth();
        if (!$token) {
            return new WF\HTTPResponse("Missing Auth Token", 401);
        }

        $body = file_get_contents('php://input');
        if (!$body) {
            return new WF\HTTPResponse("No data provided", 400);
        }

        $line = new Line(null, $token->getCollector(), $body);
        $lineRepo = new LineRepository();
        $addedLine = $lineRepo->addLine($line);
        if (!$addedLine) {
            return new WF\HTTPResponse("Failed to add line", 500);
        }
        return new WF\HTTPResponse("", 200);

    }

}