<?php

dd('ss');

use Symfony\Component\HttpFoundation\Request;

$response = $GLOBALS['kernel']->handle(Request::createFromGlobals());
$response->send();

