<?php

namespace ReactFileWatcher\Watchers;

use React\EventLoop\ExtEvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;

class EVFileWatcher extends AbstractFileWatcher
{
    public function __construct(LoopInterface $loop)
    {
        parent::__construct($loop);
        if (!($this->loop instanceof ExtEvLoop)) {
            throw new WrongLoopImplementation();
        }
    }

    public function Watch(array $pathsToWatch, $closure)
    {
        throw new FileWatcherLoopNotSupported();
    }
}