<?php

declare(strict_types=1);

namespace David\Bag;

class Bag
{
    private $data;

    public function set(string $key, $value) : Bag
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function unset(string $key) : Bag
    {
        unset($this->data[$key]);
        return $this;
    }

    public function get(string $key)
    {
        $value = null;

        if ($this->has($key)) {
            $value = $this->data[$key];
        }

        return $value;
    }

    public function has(string $key)
    {
        return isset($this->data[$key]);
    }
}
