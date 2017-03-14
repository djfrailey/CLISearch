<?php

declare(strict_types=1);

namespace David\Parser;

use \Generator;

interface ParserInterface
{
    /**
     * Method to parse the response body of a search page.
     *
     * @param  string $responseBody
     * @return Generator The parsed results.
     */
    public function parse(string $responseBody) : Generator;

    /**
     * Method to parse the next page in the search result set.
     * @return int The page number or record offset indicating
     * the next result set.
     */
    public function getNextPage() : int;
}
