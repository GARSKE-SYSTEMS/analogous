<?php
namespace Analogous\Service;

require_once __DIR__ . '/../model/Collector.php';
require_once __DIR__ . '/../repository/CollectorRepository.php';
require_once __DIR__ . '/../model/Token.php';
require_once __DIR__ . '/../repository/TokenRepository.php';

use Analogous\Model\Collector;
use Analogous\Repository\CollectorRepository;
use Analogous\Model\Token;
use Analogous\Repository\TokenRepository;

class CollectorService
{

    public static function createNewTokenFromCollector(Collector $collector)
    {
        $token = new Token(null, $collector, bin2hex(random_bytes(64)), time());
        return $token;
    }

}