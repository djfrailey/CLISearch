<?php

declare(strict_types=1);

namespace Djfrailey\Bag;

use \Generator;

/**
 * A class designed to hold other things.
 *
 * See: https://en.wikipedia.org/wiki/Bag_of_holding
 */
class Bag
{

    /**
     * Items currently held in this bag.
     * @var array
     */
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Inserts a named value into the bag.
     * @param string $key   The name of the value.
     * @param mixed $value The value
     * @return Bag
     */
    public function set(string $key, $value) : Bag
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Takes an array of names and values and inserts them
     * into the bag.
     *
     * @param array $values
     * @return Bag
     */
    public function setAll(array $values) : Bag
    {
        foreach ($values as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Removes the named item from the bag.
     *
     * @param  string $key Name of the item to remove
     * @return Bag
     */
    public function unset(string $key) : Bag
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Takes an array of names and rmeoves them from the bag.
     *
     * @param  array  $keys
     * @return Bag
     */
    public function unsetAll(array $keys) : Bag
    {
        foreach ($keys as $key) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Replaces the data in the bag with the passed in array.
     * @param  array  $values
     * @return Bag
     */
    public function fill(array $values) : Bag
    {
        $this->data = $values;
        return $this;
    }

    /**
     * Gets a named value from the bag
     *
     * @param  string $key    Name of the value to return.
     * @param  mixed $default Value to return if $key does not exist in the bag.
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $value = $default;

        if ($this->has($key)) {
            $value = $this->data[$key];
        }

        return $value;
    }

    /**
     * Creates a generator object from the data in the bag.
     *
     * @return Generator
     */
    public function toGenerator() : Generator
    {
        foreach ($this->data as $key => $value) {
            yield [$key => $value];
        }
    }

    /**
     * Checks to see if the named item exists in the bag.
     *
     * @param  string  $key
     * @return boolean
     */
    public function has(string $key) : bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Returns all items currently in the bag.
     *
     * @return array
     */
    public function all() : array
    {
        return $this->data;
    }

    /**
     * Returns the number of items in the bag.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->data);
    }
}
