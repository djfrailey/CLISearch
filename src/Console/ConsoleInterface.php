<?php

declare(strict_types=1);

namespace David\Console;

interface ConsoleInterface
{
    public function getInputStream();
    public function getOutputStream();
    
    public function readLine() : string;
    public function read(int $length = null) : string;
    public function ask(string $question) : string;

    public function writeLine(string $line) : int;
    public function write(string $message) : int;
}