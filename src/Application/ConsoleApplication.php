<?php

declare(strict_types=1);

namespace David\Application;

use David\Console\Console;

abstract class ConsoleApplication implements ApplicationInterface
{
    protected $console;

    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    abstract public function run();
}
