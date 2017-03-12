<?php

declare(strict_types=1);

namespace David\Console;

use \InvalidArgumentException;
use \RuntimeException;

class Console implements ConsoleInterface
{
    protected $input;
    protected $output;

    public function __construct()
    {
        $this->input = STDIN;
        $this->output = STDOUT;
    }

    public function getInputStream()
    {
        return $this->input;
    }

    public function getOutputStream()
    {
        return $this->output;
    }

    public function readLine() : string
    {
        $this->setInputBlocking(true);
        $line = fgets($this->input);
        $line = trim($line);
        $this->setInputBlocking(false);
        return $line;
    }

    public function read(int $length = null) : string
    {
        $this->setInputBlocking(true);
        $data = fread($this->input, $length);
        $this->setInputBlocking(false);
        return $data;
    }

    public function ask(string $question) : string
    {
        $this->write("$question: ");
        return $this->readLine();
    }

    public function writeLine(string $line) : int
    {
        return $this->write($line."\r\n");
    }

    public function write(string $message) : int
    {
        $totalMessageLength = strlen($message);
        $written = 0;

        do {
            $chunk = substr($message, $written);
            $chunkLength = strlen($chunk);
            $written += fwrite($this->output, $chunk, $chunkLength);
        } while ($written < $totalMessageLength);
        
        return $written;
    }

    protected function setInputBlocking(bool $mode)
    {
        stream_set_blocking($this->input, $mode);
    }
}
