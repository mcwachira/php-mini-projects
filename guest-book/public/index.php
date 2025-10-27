<?php

declare(strict_types=1);

const INCLUDES_DIR = __DIR__ . '/../includes';


session_start();

require_once  INCLUDES_DIR . '/router.php';

//handle request
dispatch($uri, $method);