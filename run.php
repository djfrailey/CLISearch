<?php

declare(strict_types=1);

require_once('autoload.php');

use David\Parser\GoogleParser;
use David\Seeker\GoogleSeeker;
use David\Console\Console;
use David\Application\CrawlApplication;

$console = new Console();
$seeker = new GoogleSeeker();
$parser = new GoogleParser();
$app = new CrawlApplication($console, $seeker, $parser);
$app->run();