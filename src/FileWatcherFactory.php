<?php


namespace ReactFileWatcher;

use React\EventLoop\ExtEvLoop;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Watchers\LibEVFileWatcher;
use ReactFileWatcher\Watchers\LibUVFileWatcher;

class FileWatcherFactory
{
    public static function create(LoopInterface $loop) : FileWatcherInterface
    {
        if ($loop instanceof ExtUvLoop) {
            return new LibUVFileWatcher($loop);
        }
        else if ($loop instanceof ExtEvLoop) {
            return new LibEVFileWatcher($loop);
        }
        else {
            // TODO: each one of these types should be implemented.
            throw new FileWatcherLoopNotSupported();
        }
    }
}