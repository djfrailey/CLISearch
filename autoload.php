<?php

declare(strict_types=1);

require_once('src/Autoload/PSR4Loader.php');

$dir = __DIR__;
$autoloader = new David\Autoload\PSR4Loader($dir);
$autoloader->registerNamespaceBaseDirectory("David\\", "src");
