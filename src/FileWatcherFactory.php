<?php


namespace ReactFileWatcher;

use React\EventLoop\ExtEvLoop;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Watchers\DefaultFileWatcher;
use ReactFileWatcher\Watchers\EVFileWatcher;
use ReactFileWatcher\Watchers\LibUVFileWatcher;

class FileWatcherFactory
{
    public static function create(LoopInterface $loop) : FileWatcherInterface
    {
        if ($loop instanceof ExtUvLoop) {
            return new LibUVFileWatcher($loop);
        }
        else if ($loop instanceof ExtEvLoop) {
            return new EVFileWatcher($loop);
        }
        else {
            return new DefaultFileWatcher($loop);
        }
    }
}