<?php

namespace David\Parser;

use \Generator;

interface ParserInterface
{
    public function parse(string $responseBody) : Generator;
    public function getNextPage() : int;
}