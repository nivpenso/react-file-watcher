<?php

namespace ReactFileWatcher\PathObjects;

class PathWatcher
{
    protected string $pathToWatch;
    protected bool $isRecursiveWatch;
    protected array $suffixToExclude;

    public function __construct(string $pathToWatch, bool $setRecursiveWatch = true, array $suffixToExclude = [])
    {
        $this->pathToWatch = $pathToWatch;
        $this->isRecursiveWatch = $setRecursiveWatch;
        $this->suffixToExclude = $suffixToExclude;
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

}