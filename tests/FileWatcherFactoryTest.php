<?php

use React\EventLoop\ExtLibevLoop;
use React\EventLoop\ExtEvLoop;
use React\EventLoop\StreamSelectLoop;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\FileWatcherFactory;

it('should return ExtUvFileWatcher when loop implementation is ExtUVLoop',function () {
    $loop = new \React\EventLoop\ExtUvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(\ReactFileWatcher\Watchers\LibUVFileWatcher::class);
});

// TODO: handle this
it('should return ExtEvFileWatcher when loop implementation is ExtEVLoop',function ($loopType) {
    uopz_flags($loopType, null, 0);
    $loop = $this->getMockBuilder($loopType)->getMock();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(\ReactFileWatcher\Watchers\LibEVFileWatcher::class);
})->with([StreamSelectLoop::class])->throws(FileWatcherLoopNotSupported::class);

it('should throw FileWatcherLoopNotSupported when loop implementation is type of unknown LoopInterface',function () {
    $loop = $this->getMockBuilder(\React\EventLoop\LoopInterface::class)->getMock();
    $fileWatcher = FileWatcherFactory::create($loop);
})->throws(FileWatcherLoopNotSupported::class);