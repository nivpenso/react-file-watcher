<?php

namespace ReactFileWatcher\Watchers;

use \React\EventLoop\LoopInterface;
use ReactFileWatcher\FileWatcherInterface;

abstract class AbstractFileWatcher implements FileWatcherInterface
{
    protected LoopInterface  $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public abstract function Watch(array $pathsToWatch, \Closure $closure);
}