<?php

declare(strict_types=1);

namespace Djfrailey\Application;

use Djfrailey\Parser\ParserInterface;
use Djfrailey\Seeker\Seeker;
use Djfrailey\Console\ConsoleInterface;
use Djfrailey\Bag\Bag;
use \Generator;

class CrawlApplication
{
    /**
     * Search input state identifier.
     */
    const INPUT_STATE_SEARCH = 1;
    /**
     * Pagination input state identifier.
     */
    const INPUT_STATE_PAGINATE = 2;
    
    /**
     * The current input state of the application.
     * @var int
     */
    private $inputState = self::INPUT_STATE_SEARCH;

    /**
     * Flag to indicate wether or not the run loop should continue.
     * @var boolean Defaults to true.
     */
    private $shouldRun = true;

    /**
     * Bag to hold the current search parameters.
     * @var Bag
     */
    private $searchParams;

    /**
     * Variable reference to the crawlers current page parser.
     * @var ParserInterface
     */
    private $parser;

    /**
     * Variable reference to the console representation.
     * @var ConsoleInterface
     */
    private $console;

    /**
     *
     * Variable reference to the crawlers current seeker.
     * @var SeekerInterface
     */
    private $seeker;

    public function __construct(
        ConsoleInterface $console,
        Seeker $seeker,
        ParserInterface $parser
    ) {
        $this->console = $console;
        $this->seeker = $seeker;
        $this->parser = $parser;
        $this->searchParams = new Bag();
    }

    /**
     * The main logic for the application.
     *
     * This method passes control to the search input loop and paginate input loop
     * depending on the current application state.
     * After the input loop exits any search parameters are dispatched to the seeker.
     * The seekers response is then dispatched to the parser.
     * The parsed response is then output to the console.
     *
     */
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

    /**
     * Handles the input loop to run after the initial application search.
     *
     * If the parser has not detected any more pages then
     * this function sets the application state back to INPUT_STATE_SEARCH
     * and then exits.
     *
     */
    private function handlePaginateInputLoop()
    {
        $nextPage = $this->parser->getNextPage();

        if ($nextPage > 0) {
            $getNextPage = $this->getUserRequestsNextPage();

            if ($getNextPage === false) {
                $nextPage = 0;
                $this->setInputState(self::INPUT_STATE_SEARCH);
            }
        } else {
            $this->setInputState(self::INPUT_STATE_SEARCH);
        }

        $this->searchParams->set('page', $nextPage);
    }

    private function handleSearchInputLoop()
    {
        $searchTerm = $this->getSearchTerm();
        $this->searchParams->set('searchTerm', $searchTerm);
        $this->setInputState(self::INPUT_STATE_PAGINATE);
    }

    /**
     * Prompts the user to indicate if they want the next page of results.
     *
     * This method continues to loop until a valid response is received.
     *
     * @return bool True if the user wants the next page of results.
     */
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

    /**
     * Prompts the user to enter a search query.
     *
     * This method continues to loop until a non empty search query has
     * been entered.
     *
     * @return string The search query entered by the user.
     */
    private function getSearchTerm() : string
    {
        $searchTerm = "";

        while ($searchTerm == false) {
            $searchTerm = $this->console->ask("Enter Query (SIGINT Exits)");
        }

        return $searchTerm;
    }

    /**
     * Outputs the passed search result set.
     *
     * @param  Generator $results A set of results from the parser
     */
    private function outputResults(Generator $results)
    {
        $output = $this->console->getOutputStream();

        foreach ($results as $result) {
            $output->writeLine('---');
            $output->writeLine($result['title']);
            $output->writeLine($result['href']);
            $output->writeLine('---');
        }
    }

    /**
     * Method to set the current input state.
     * @param int $state One of the INPUT_STATE_* constants
     */
    private function setInputState(int $state)
    {
        $this->inputState = $state;
    }
}
