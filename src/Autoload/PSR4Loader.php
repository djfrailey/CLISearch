<?php

declare (strict_types=1);

namespace Djfrailey\Autoload;

/**
 * Simple class to handle the autoloading of PHP files.
 */
class PSR4Loader
{

    /**
     * The base directory in which to look for dependencies.
     * @var string
     */
    private $baseDirectory;

    /**
     * A map used to hold namespaces to directories.
     * @var array
     */
    private $namespaceToDirectoryMap = [];

    public function __construct(string $baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
        spl_autoload_register($callback = [$this, 'load'], $throw = false);
    }

    /**
     * Attempts to load the requested class.
     * @param  string $class Fully qualified class name of the requested class.
     */
    public function load(string $class)
    {
        $classDirectory = $this->classStringToDirectory($class);
        $classDirectory = $this->addBaseDirectories($classDirectory);
        $classDirectory .= '.php';

        if (file_exists($classDirectory) === true) {
            require_once($classDirectory);
        }
    }

    public function registerNamespaceBaseDirectory(string $namespace, string $directory)
    {
        $namespace = str_replace("\\", "/", $namespace);
        $this->namespaceToDirectoryMap[$namespace] = $directory;
    }

    private function classStringtoDirectory(string $class)
    {
        return str_replace("\\", "/", $class);
    }

    /**
     * Adds the base directory and maps any namespace names to
     * their corresponding directories listed in $namespaceToDirectoryMap
     * @param string $toDirectory The mutated class directory
     */
    private function addBaseDirectories(string $toDirectory)
    {
        foreach ($this->namespaceToDirectoryMap as $namespace => $baseDirectory) {
            $baseDirectory = rtrim($baseDirectory, '/');
            $baseDirectory .= '/';
            
            $namespace = preg_quote($namespace, '#');
            $toDirectory = preg_replace("#$namespace#", $baseDirectory, $toDirectory);
        }

        $toDirectory = "$this->baseDirectory/$toDirectory";

        return $toDirectory;
    }
}
