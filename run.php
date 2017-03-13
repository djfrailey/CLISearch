<?php

declare(strict_types=1);

require_once('autoload.php');

use David\Parser\GoogleParser;
use David\Seeker\GoogleSeeker;
use David\Console\Console;
use David\Application\CrawlApplication;
use David\Stream\Stream;

$inputStream = new Stream(STDIN);
$outputStream = new Stream(STDOUT);

$console = new Console($inputStream, $outputStream);
$seeker = new GoogleSeeker();
$parser = new GoogleParser();
$app = new CrawlApplication($console, $seeker, $parser);
$app->run();
