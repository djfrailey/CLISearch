<?php

declare(strict_types=1);

namespace David\State;

class PaginationState extends CrawlApplicationState
{
    public function run()
    {
        $nextPage = $this->sharedData->get('nextPage');
        
        if ($nextPage > 0) {
            $getNextPage = $this->getUserRequestsNextPage();
            $this->sharedData->set('getNextPage', $getNextPage);
        }
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
}
