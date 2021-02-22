<?php

namespace ReactFileWatcher\Watchers;

use Closure;
use React\EventLoop\ExtEvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;

class LibEVFileWatcher extends AbstractFileWatcher
{
    public function __construct(LoopInterface $loop)
    {
        parent::__construct($loop);
        if (get_class($this->loop) !== ExtEvLoop::class) {
            throw new WrongLoopImplementation();
        }
    }

    public function Watch(array $pathsToWatch, Closure $closure)
    {
        throw new FileWatcherLoopNotSupported();
    }
}