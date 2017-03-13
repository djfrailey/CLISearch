<?php

declare(strict_types=1);

namespace David\Application;

use David\Parser\ParserInterface;
use David\Console\Console;
use David\Seeker\Seeker;
use David\Bag\Bag;
use David\State\CrawlApplicationState;
use \Generator;

class CrawlApplication extends ConsoleApplication
{
    const INPUT_STATE_SEARCH = 1;
    const INPUT_STATE_PAGINATE = 2;
    
    private $inputState = self::INPUT_STATE_SEARCH;
    private $shouldRun = true;
    private $searchParams;
    private $parser;
    private $seeker;

    public function __construct(Console $console, Seeker $seeker, ParserInterface $parser)
    {
        parent::__construct($console);
        $this->seeker = $seeker;
        $this->parser = $parser;
        $this->searchParams = new Bag();
    }

    public function run()
    {
        while ($this->shouldRun) {
            
            switch ($this->inputState) {
                case self::INPUT_STATE_SEARCH:
                    $this->handleSearchInputLoop();
                break;
                case self::INPUT_STATE_PAGINATE:
                        $this->handlePaginateInputLoop();
                break;
            }

            $searchTerm = $this->searchParams->get('searchTerm', '');
            $page = $this->searchParams->get('page', 0);

            if ($searchTerm) {
                $seekResponse = $this->seeker->seek($searchTerm, $page);
                $parsedData = $this->parser->parse($seekResponse);

                $this->outputResults($parsedData);
            }
        }
    }

    private function handlePaginateInputLoop()
    {
        $nextPage = $this->parser->getNextPage();

        if ($nextPage > 0) {
            $getNextPage = $this->getUserRequestsNextPage();

            if ($getNextPage === false) {
                $nextPage = 0;
                $this->setInputState(self::INPUT_STATE_SEARCH);
            }
        }

        $this->searchParams->set('page', $nextPage);
    }

    private function handleSearchInputLoop()
    {
        $searchTerm = $this->getSearchTerm();
        $this->searchParams->set('searchTerm', $searchTerm);
        $this->setInputState(self::INPUT_STATE_PAGINATE);
    }

    private function getUserRequestsNextPage() : bool
    {
        $validInputReceived = false;
        $userRequestsNextPage = false;

        while ($validInputReceived === false) {
            $input = $this->console->ask('Next Page [Y/N]?');
            $input = strtolower($input);

            if ($input === 'y' || $input === 'yes') {
                $userRequestsNextPage = true;
                $validInputReceived = true;
            } elseif ($input === 'n' || $input === 'no') {
                $validInputReceived = true;
            }
        }

        return $userRequestsNextPage;
    }

    private function getSearchTerm() : string
    {
        $searchTerm = "";

        while ($searchTerm == false) {
            $searchTerm = $this->console->ask("Enter Query (SIGINT Exits)");
        }

        return $searchTerm;
    }

    private function outputResults(Generator $results)
    {
        $output = $this->console->getOutputStream();

        foreach($results as $result) {
            $output->writeLine('---');
            $output->writeLine($result['title']);
            $output->writeLine($result['href']);
            $output->writeLine('---');
        }
    }

    private function setInputState(int $state)
    {
        $this->inputState = $state;
    }
}
