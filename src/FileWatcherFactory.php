<?php


namespace ReactFileWatcher;

use React\EventLoop\ExtEventLoop;
use React\EventLoop\ExtEvLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtLibevLoop;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Watchers\LibEVFileWatcher;
use ReactFileWatcher\Watchers\LibUVFileWatcher;

class FileWatcherFactory
{
    public static function create(LoopInterface $loop) : FileWatcherInterface
    {
        $loopClassType = get_class($loop);
        // @codeCoverageIgnoreStart
        switch ($loopClassType) {
            case ExtUvLoop::class:
                return new LibUVFileWatcher($loop);
            case ExtEvLoop::class:
                return new LibEVFileWatcher($loop);
            case ExtLibevLoop::class:
            case ExtLibeventLoop::class:
            case ExtEventLoop::class:
            case StreamSelectLoop::class:
                // TODO: each one of these types should be implemented.
                throw new FileWatcherLoopNotSupported();
            default:
                throw new FileWatcherLoopNotSupported();

        }
    }
}