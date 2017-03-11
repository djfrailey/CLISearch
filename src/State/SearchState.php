<?php

declare(strict_types=1);

namespace David\State;

class SearchState extends CrawlApplicationState
{
    public function run()
    {
        $getNextPage = $this->sharedData->get('getNextPage');
        $searchTerm = $this->sharedData->get('searchTerm');
        
        if ($getNextPage !== true) {
            $searchTerm = $this->getSearchTerm();
        }
        
        $nextPage = 0;

        if ($getNextPage === true) {
            $nextPage = $this->sharedData->get('nextPage');
        }

        $searchResponse = $this->seeker->seek($searchTerm, $nextPage);
        
        $parsedData = $this->parser->parse($searchResponse);

        foreach($parsedData as $data) {
            $this->console->writeLine('---');
            $this->console->writeLine($data['title']);
            $this->console->writeLine($data['href']);
            $this->console->writeLine('---');
        }

        $nextPage = $this->parser->getNextPage();

        $this->sharedData->set('searchTerm', $searchTerm);
        $this->sharedData->set('nextPage', $nextPage);
    }

    private function getSearchTerm() : string
    {
        $searchTerm = "";

        while ($searchTerm == false) {
            $searchTerm = $this->console->ask("Enter Query (SIGINT Exits)");
        }

        return $searchTerm;
    }
}