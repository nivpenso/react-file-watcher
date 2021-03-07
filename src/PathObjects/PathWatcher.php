<?php

namespace ReactFileWatcher\PathObjects;

use Exception;
use ReactFileWatcher\Exceptions\PathDoesntExist;

class PathWatcher
{
    protected string $pathToWatch;
    protected bool $isRecursiveWatch;
    protected array $suffixToExclude;
    protected bool $isDir;
    protected string $dirPart;
    protected string $filenamePart;

    public function __construct(string $pathToWatch, bool $setRecursiveWatch = true, array $suffixToExclude = [])
    {
        $this->pathToWatch = $pathToWatch;
        $this->isRecursiveWatch = $setRecursiveWatch;
        $this->suffixToExclude = $suffixToExclude;
        if (is_dir($pathToWatch)) {
            $this->isDir = true;
            $this->dirPart = $pathToWatch;
        }
        else {
            if (is_file($pathToWatch)) {
                $this->isDir = false;
                $this->filenamePart = basename($this->pathToWatch);
                $this->dirPart = dirname($this->pathToWatch);
            }
            else {
                throw new PathDoesntExist();
            }
        }
    }

    /**
     * @return string
     */
    public function getPathToWatch(): string
    {
        return $this->pathToWatch;
    }

    /**
     * @return bool
     */
    public function isRecursiveWatch(): bool
    {
        return $this->isRecursiveWatch;
    }

    /**
     * @return array
     */
    public function getSuffixToExclude(): array
    {
        return $this->suffixToExclude;
    }

    /**
     * @return bool
     */
    public function isDir(): bool
    {
        return $this->isDir;
    }

    public function isFile(): bool {
        return !$this->isDir;
    }

    public function getDirPart() :string {
        return $this->dirPart;
    }

    public function getFilenamePart() :string {
        if ($this->isDir()) {
            throw new Exception('Try to get filename on a dir path');
        }
        return $this->filenamePart;
    }
}