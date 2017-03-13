<?php

declare(strict_types=1);

namespace David\Console;

use David\Stream\Stream;

use \InvalidArgumentException;
use \RuntimeException;

class Console implements ConsoleInterface
{
    protected $input;
    protected $output;

    public function __construct(Stream $input, Stream $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function getInputStream() : Stream
    {
        return $this->input;
    }

    public function getOutputStream() : Stream
    {
        return $this->output;
    }

    public function ask(string $question) : string
    {
        $this->output->write("$question: ");
        $this->input->setBlocking(true);
        $answer = $this->input->readLine();
        return $answer;
    }
}
