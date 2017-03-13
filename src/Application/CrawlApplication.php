<?php

declare(strict_types=1);

namespace David\Application;

use David\Parser\ParserInterface;
use David\Console\Console;
use David\Seeker\Seeker;
use David\Bag\Bag;
use David\State\CrawlApplicationState;
use David\State\SearchState;
use David\State\PaginationState;

class CrawlApplication extends ConsoleApplication
{
   
    private $shouldRun = true;
    private $applicationState;
    private $parser;
    private $seeker;
    private $sharedApplicationData;

    public function __construct(Console $console, Seeker $seeker, ParserInterface $parser)
    {
        parent::__construct($console);
        
        $this->seeker = $seeker;
        $this->parser = $parser;
        $this->sharedApplicationData = new Bag();
        
        $defaultState = $this->createSearchState();
        $this->setState($defaultState);
    }

    public function run()
    {
        while ($this->shouldRun) {
            $this->applicationState->run();
            $this->nextState();
        }
    }

    public function setState(CrawlApplicationState $state)
    {
        $this->applicationState = $state;
    }

    private function nextState()
    {
        $nextState = null;
        
        if ($this->applicationState instanceof SearchState) {
            $nextState = $this->createPaginationState();
        }

        if ($this->applicationState instanceof PaginationState) {
            $nextState = $this->createSearchState();
        }

        $this->setState($nextState);
    }

    private function createSearchState() : CrawlApplicationState
    {
        return new SearchState($this->console, $this->seeker, $this->parser, $this->sharedApplicationData);
    }

    private function createPaginationState() : CrawlApplicationState
    {
        return new PaginationState($this->console, $this->seeker, $this->parser, $this->sharedApplicationData);
    }
}
