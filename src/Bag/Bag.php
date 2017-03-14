<?php

declare(strict_types=1);

namespace David\Bag;
use \Generator;

class Bag
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function set(string $key, $value) : Bag
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function setAll(array $values) : Bag
    {
        foreach($values as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function unset(string $key) : Bag
    {
        unset($this->data[$key]);
        return $this;
    }

    public function unsetAll(array $values) : Bag
    {
        foreach($values as $key => $value) {
            unset($this->data[$key]);
        }

        return $this;
    }

    public function fill(array $values) : Bag
    {
        $this->data = $values;
        return $this;
    }

    public function get(string $key, $default = null)
    {
        $value = $default;

        if ($this->has($key)) {
            $value = $this->data[$key];
        }

        return $value;
    }

    public function toGenerator() : Generator
    {
        foreach($this->data as $key => $value) {
            yield [$key => $value];
        }
    }

    public function has(string $key) : bool
    {
        return isset($this->data[$key]);
    }

    public function all() : array
    {
        return $this->data;
    }

    public function count() : int
    {
        return count($this->data);
    }
}
