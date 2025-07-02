<?php

require_once __DIR__ . '/../util/CSRFHelper.php';
require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../service/UserService.php';
require_once __DIR__ . '/../model/User.php';

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use Analogous\Repository\UserRepository;
use Analogous\Service\UserService;
use Analogous\Model\User;

use VeloFrame as WF;

# @route api/users/create
class APICreateUserHandler extends WF\DefaultPageController
{
    public function handleGet(array $params)
    {
        // CSRF validation
        AuthHelper::requireLogin();

        $csrfParam = $_GET['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($csrfParam)) {
            return new WF\HTTPResponse("Invalid CSRF token", 403);
        }

        $username = $_GET['username'] ?? null;
        $password = $_GET['password'] ?? null;
        if (!$username || !$password) {
            return new WF\HTTPResponse("Missing 'username' or 'password' parameter", 400);
        }

        // create user
        $created = UserService::createUser($username, $password);
        if (!$created) {
            return new WF\HTTPResponse("Failed to create user", 500);
        }

        $resp = $created->toArray();
        $resp['csrf_token'] = CSRFHelper::generateToken();
        return json_encode($resp);
    }
}
