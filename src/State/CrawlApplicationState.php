<?php

namespace David\State;

use David\Console\ConsoleInterface;
use David\Seeker\Seeker;
use David\Bag\Bag;
use David\Parser\ParserInterface;

abstract class CrawlApplicationState
{
    protected $console;
    protected $sharedData;
    protected $seeker;

    public function __construct(ConsoleInterface $console, Seeker $seeker, ParserInterface $parser, Bag $sharedData)
    {
        $this->console = $console;
        $this->sharedData = $sharedData;
        $this->seeker = $seeker;
        $this->parser = $parser;
    }

    abstract public function run();
}
