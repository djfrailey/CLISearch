<?php

declare(strict_types=1);

namespace David\Autoload;

class PSR4Loader
{
    private $baseDirectory;
    private $namespaceToDirectoryMap = [];

    public function __construct(string $baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
        spl_autoload_register($callback = [$this, 'load'], $throw = false);
    }

    public function load(string $class)
    {
        $classDirectory = $this->classStringToDirectory($class);
        $classDirectory = $this->addBaseDirectories($classDirectory);
        $classFile = rtrim($classDirectory, '.php');
        $classFile .= '.php';

        if (file_exists($classFile) === true) {
            require_once($classFile);
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

    private function addBaseDirectories(string $toDirectory)
    {
        foreach($this->namespaceToDirectoryMap as $namespace => $baseDirectory) {
            $baseDirectory = rtrim($baseDirectory, '/');
            $baseDirectory .= '/';
            
            $namespace = preg_quote($namespace, '#');
            $toDirectory = preg_replace("#$namespace#", $baseDirectory, $toDirectory);
        }

        $toDirectory = "$this->baseDirectory/$toDirectory";

        return $toDirectory;
    }
}