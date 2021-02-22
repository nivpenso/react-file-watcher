<?php

namespace ReactFileWatcher\Watchers;

use Closure;
use \React\EventLoop\LoopInterface;
use ReactFileWatcher\FileWatcherInterface;
use ReactFileWatcher\PathObjects\PathWatcher;

abstract class AbstractFileWatcher implements FileWatcherInterface
{
    protected LoopInterface  $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public abstract function Watch(array $pathsToWatch, Closure $closure);

    protected function onChangeDetected(string $changedFileName, PathWatcher $pathWatcher, Closure $closure): void {
        $suffixesToExclude = $pathWatcher->getSuffixToExclude();
        foreach ($suffixesToExclude as $suffix) {
            if ($this->isFilenameHasSuffix($changedFileName, $suffix)) {
                // exits - this file detection should not be notified
                return;
            }
        }
        $closure($changedFileName);
    }

    protected function isFilenameHasSuffix($filename, $suffix) : bool {
        return substr_compare($filename, $suffix, -strlen($suffix)) === 0;
    }
}