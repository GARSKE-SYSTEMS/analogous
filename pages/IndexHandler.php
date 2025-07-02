<?php

require_once __DIR__ . '/../util/AuthHelper.php';

/**
 * IndexHandler.php
 * 
 * Example Index Page Handler
 * 
 * @author Patrick Matthias Garske <patrick@garske.link>
 * @since 0.1
 */

use Analogous\Util\AuthHelper;
use VeloFrame as WF;

# @route index
class IndexHandler extends WF\DefaultPageController
{

    public function handleGet(array $params)
    {
        AuthHelper::requireLogin();
        return "";

    }

}