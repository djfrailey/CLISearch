<?php

declare (strict_types=1);

require_once('autoload.php');

use Djfrailey\Parser\GoogleParser;
use Djfrailey\Seeker\GoogleSeeker;
use Djfrailey\Console\Console;
use Djfrailey\Application\CrawlApplication;
use Djfrailey\Stream\Stream;

$inputStream = new Stream(STDIN);
$outputStream = new Stream(STDOUT);

$console = new Console($inputStream, $outputStream);
$seeker = new GoogleSeeker();
$parser = new GoogleParser();
$app = new CrawlApplication($console, $seeker, $parser);
$app->run();
