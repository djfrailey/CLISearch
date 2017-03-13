<?php

declare(strict_types=1);

namespace David\Console;

use David\Stream\Stream;

interface ConsoleInterface
{
    public function getInputStream() : Stream;
    public function getOutputStream() : Stream;
    
    public function ask(string $question) : string;
}
