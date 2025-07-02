<?php

require_once __DIR__ . '/../util/AuthHelper.php';
require_once __DIR__ . '/../util/CSRFHelper.php';

/**
 * IndexHandler.php
 * 
 * Example Index Page Handler
 * 
 * @author Patrick Matthias Garske <patrick@garske.link>
 * @since 0.1
 */

use Analogous\Util\AuthHelper;
use Analogous\Util\CSRFHelper;
use VeloFrame as WF;
use VeloFrame\Template;

# @route index
class IndexHandler extends WF\DefaultPageController
{

    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();
        $tpl = new WF\Template("index");
        $tpl->includeTemplate("head", new Template("std_head"));
        $tpl->includeTemplate("js_deps", new Template("js_deps"));
        $tpl->setVariable("csrf_token", CSRFHelper::generateToken());

        return $tpl->output();
    }

}