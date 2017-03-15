<?php

declare(strict_types=1);

namespace Djfrailey\Console;

use Djfrailey\Stream\Stream;

use \InvalidArgumentException;
use \RuntimeException;

class Console implements ConsoleInterface
{

    /**
     * Reference to the current input stream.
     * @var Stream
     */
    protected $input;

    /**
     * Reference to the current output stream.
     * @var Stream
     */
    protected $output;

    public function __construct(Stream $input, Stream $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function getInputStream() : Stream
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function getOutputStream() : Stream
    {
        return $this->output;
    }

    /**
     * @inheritDoc
     */
    public function ask(string $question) : string
    {
        $this->output->write("$question: ");
        $this->input->setBlocking(true);
        $answer = $this->input->readLine();
        return $answer;
    }
}
