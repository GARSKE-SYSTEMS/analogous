<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../util/CSRFHelper.php';

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use VeloFrame as WF;
use VeloFrame\Template;

# @route login
class LoginHandler extends WF\DefaultPageController
{
    public function handleGet(array $params)
    {
        // Show the login form
        $tpl = new Template("login");
        $tpl->includeTemplate("head", new Template("std_head"));
        $tpl->includeTemplate("js_deps", new Template("js_deps"));
        $tpl->setVariable("csrf_input", CSRFHelper::insertHiddenInput());
        return $tpl->output();
    }

    public function handlePost(array $params)
    {
        // CSRF validation
        $posted = $_POST['csrf_token'] ?? null;
        if (!CSRFHelper::validateToken($posted)) {
            $tpl = new Template("login");
            $tpl->includeTemplate("head", new Template("std_head"));
            $tpl->includeTemplate("js_deps", new Template("js_deps"));
            $tpl->setVariable("error", "Invalid CSRF token");
            $tpl->setVariable("csrf_input", CSRFHelper::insertHiddenInput());
            return $tpl->output();
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!AuthHelper::login($username, $password)) {
            $tpl = new Template("login");
            $tpl->includeTemplate("head", new Template("std_head"));
            $tpl->includeTemplate("js_deps", new Template("js_deps"));
            $tpl->setVariable("error", "Invalid username or password");
            $tpl->setVariable("csrf_input", CSRFHelper::insertHiddenInput());
            return $tpl->output();
        }

        // On successful login, redirect to dashboard
        header('Location: /');
        exit();
    }
}
