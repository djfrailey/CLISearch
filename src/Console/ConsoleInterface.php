<?php

declare(strict_types=1);

namespace David\Console;

use David\Stream\Stream;

interface ConsoleInterface
{
    /**
     * Returns a reference to the current input stream
     * @return Stream
     */
    public function getInputStream() : Stream;

    /**
     * Returns a reference to the current output stream
     * @return Stream
     */
    public function getOutputStream() : Stream;
    
    /**
     * Asks the user for input
     *
     * @param  string $question Message to be displayed when asking the user for input.
     * @return string
     */
    public function ask(string $question) : string;
}
