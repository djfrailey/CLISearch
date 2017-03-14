<?php

declare(strict_types=1);

namespace David\Stream;

use \RuntimeException;
use \InvalidArgumentException;

/**
 * A simple object wrapper over a stream resource.
 */
class Stream
{
    /**
     * Constant to identify readable streams.
     */
    const STREAM_READABLE = 1;

    /**
     * Constant to identify writable streams.
     */
    const STREAM_WRITABLE = 2;

    /**
     * Constant to identify binary streams.
     */
    const STREAM_BINARY = 4;

    /**
     * Variable for the open stream resource.
     * @var resource
     */
    protected $resource;
    
    /**
     * A map of file modes to stream attributes.
     * @var array
     */
    protected $modes = [
        'r'  => self ::STREAM_READABLE,
        'r+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'w'  => self ::STREAM_WRITABLE,
        'w+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'a'  => self ::STREAM_READABLE,
        'a+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'x'  => self ::STREAM_READABLE,
        'x+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'c'  => self ::STREAM_WRITABLE,
        'c+' => self::STREAM_READABLE | self::STREAM_WRITABLE,
        'wb' => self::STREAM_WRITABLE | self::STREAM_BINARY,
        'rb' => self::STREAM_READABLE | self::STREAM_BINARY,
    ];

    /**
     * @throws InvalidArgumentException
     * @param resource $resource The resource to wrap
     */
    public function __construct($resource)
    {
        if (is_resource($resource) === false) {
            throw new InvalidArgumentException("Argument passed to constructor must be a resource");
        }

        $this->resource = $resource;
    }

    /**
     * Ensures the resource has been closed when the object is garbage collected.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Seeks to the beginning of the stream and flushes its contents.
     * @throws  RuntimeException is thrown if reading the stream contents fails.
     * @return string
     */
    }

    /**
     * Closes the current stream resource.
     * @return void
     */
    public function close()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    /**
     * Detaches the current stream resource from the object and passes it to the caller.
     * @return resource
     */
    public function detach()
    {
        $copy = $this->resource;

        $this->resource = null;

        return $copy;
    }

    public function getSize()
    /**
     * Attempts to seek to the end of a stream and report the total size.
     * @return int
     */
    {
        $size = null;

        if ($this->isSeekable()) {
            $this->seek(0, SEEK_END);
            $size = $this->tell();
        }

        return $size;
    }

    /**
     * @return int Current position in the stream.
     */
    public function tell() : int
    {
        return ftell($this->resource);
    }

    /**
     * @return bool True if the streams end has been reached.
     */
    public function eof() : bool
    {
        return feof($this->resource);
    }

    /**
     * Changes the blocking status of a stream.
     * @param bool $blocking Setting this to true will attempt to put the stream in blocking mode.
     * @return bool True if the blocking mode has been set successfully.
     */
    public function setBlocking(bool $blocking = false) : bool
    {
        return stream_set_blocking($this->resource, $blocking);
    }

    /**
     * Reads the streams blocking status from its meta data.
     * @return bool True if the stream is blocked.
     */
    public function getBlocking() : bool
    {
        return (bool) $this->getMetadata('blocked');
    }

    /**
     * Reads the streams seekable status from its meta data.
     * @return bool True if the stream is seekable.
     */
    public function isSeekable() : bool
    {
        return $this->getMetadata('seekable');
    }

    public function seek($offset, $whence = SEEK_SET)
    /**
     * Attempts to seek to the specified position in the stream.
     * @param  int $offset The byte offset to try to seek to.
     * @param  int $whence One of the three fseek constants (http://php.net/manual/en/function.fseek.php)
     * @throws RuntimeException if the stream is not seekable.
     * @return bool True if the seek is successful. False on failure.
     */
    {
        if ($this->isSeekable() === false) {
            throw new RuntimeException("Stream is not seekable.");
        }

        return fseek($this->resource, $$offset, $whence);
    }

    /**
     * Returns the streams internal pointer back to the beginning.
     * @throws  RuntimeException if the stream is not seekable.
     * @return [type] [description]
     */
    public function rewind()
    {
        if ($this->isSeekable() === false) {
            throw new RuntimeException("Cannot rewind stream. Stream is not seekable.");
        }

        $this->seek(0, SEEK_SET);
    }

    /**
     * @return True if the stream is writable.
     */
    public function isWritable()
    {
        $mode = $this->getMetadata('mode');
        return $this->modes[$mode] & self::STREAM_WRITABLE;
    }

    public function write($string) : int
    /**
     * Writes the given string to the stream.
     * @param  string   $string The string of data to write to the stream.
     * @param  int|null $length The number of bytes to write to the stream.
     * @throws RuntimeException if the stream is not writable.
     * @throws RuntimeException if an error occurs while writing to the stream.
     * @return int              The number of bytes written to the stream.
     */
    {
        if ($this->isWritable() === false) {
            throw new RuntimeException("Cannot write data to stream. Stream is not writable.");
        }

        $written = fwrite($this->resource, $string);

        if ($written === false) {
            throw new RuntimeException("There was an error while writing to the stream.");
        }

        return $written;
    }

    public function writeLine($string) : int
    /**
     * Writes the given string followed by a newline to the stream.
     * @param  string $string The string of data to write.
     * @return int The number of bytes written to the stream.
     */
    {
        return $this->write($string . PHP_EOL);
    }
    
    public function isReadable() 
    /**
     * @return boolean True if the stream is readable.
     */
    {
        $mode = $this->getMetadata('mode');
        return $this->modes[$mode] & self::STREAM_READABLE;
    }
    
    /**
     * Reads up to $length in bytes.
     * @param  integer $length of bytes to read from the stream.
     * @return string  The data read from the stream.
     */
    public function read($length = 1024) : String
    {
        $read = fread($this->resource, $length);

        if ($this->getBlocking() === false
            && $read === false) {
            throw new RuntimeException("There was an error while reading from the stream.");
        }

        return $read;
    }

    /**
     * Reads a line from the stream and removes any trailing whitespace.
     * @throws RuntimeException if reading from the stream fails.
     * @return string The data read from the stream.
     */
    public function readLine() : String
    {
        $read = fgets($this->resource);

        if ($this->getBlocking() === false
            && $read === false) {
            throw new RuntimeException("There was an error while reading from the stream.");
        }

        $read = trim($read);

        return $read;
    }

    /**
     * Retrieves the contents of the stream
     * @return string
     */
    public function getContents() : String
    {
        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new RuntimeException("There was an error retrieving the contents of the stream.");
        }

        return $contents;
    }

    /**
     * Reads meta data from the stream resource.
     * @param  string $key They key of the corresponding meta value to retrieve.
     * @return mixed
     *
     * If a key is not provided then this function returns all stream meta data.
     * If a key is provided and does not exist in the meta data array then this function
     * returns a null value.
     * Otherwise this function returns the meta value corresponding to the passed meta key.
     */
    public function getMetadata(string $key = null)
    {
        $meta = stream_get_meta_data($this->resource);

        if (is_null($key) === true) {
            return $meta;
        }

        if (isset($meta[$key])) {
            return $meta[$key];
        }

        return null;
    }

    /**
     * @return resource Returns the wrapped stream resource.
     */
    public function getResource()
    {
        return $this->resource;
    }
}
