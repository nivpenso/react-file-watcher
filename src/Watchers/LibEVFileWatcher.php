<?php

namespace ReactFileWatcher\Watchers;

use React\EventLoop\ExtLibevLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;

class LibEVFileWatcher extends AbstractFileWatcher
{
    public function __construct(LoopInterface $loop)
    {
        parent::__construct($loop);
        if (get_class($this->loop) !== ExtLibevLoop::class) {
            throw new WrongLoopImplementation();
        }

        throw new FileWatcherLoopNotSupported();
    }

    public function Watch(array $pathsToWatch, \Closure $closure)
    {
        throw new FileWatcherLoopNotSupported();
    }
}