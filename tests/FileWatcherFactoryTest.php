<?php

use React\EventLoop\LoopInterface;
use React\EventLoop\ExtEventLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtLibevLoop;
use React\EventLoop\ExtEvLoop;
use \React\EventLoop\ExtUvLoop;
use React\EventLoop\StreamSelectLoop;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\FileWatcherFactory;

it('should return ExtUvFileWatcher when loop implementation is ExtUVLoop',function () {
    $loop = new ExtUvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(\ReactFileWatcher\Watchers\LibUVFileWatcher::class);
})->group("LibUV");

it('should return ExtEvFileWather when loop implementation is ExtEVLoop',function () {
    $loop = new ExtEvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(\ReactFileWatcher\Watchers\LibEVFileWatcher::class);
})->group("LibUV");

// TODO: need to add into the require-dev of the composer all the ext-packages to allow this test to run.
it('should throw exception of file watcher not implemented',function ($loopType) {
    // phpunit doesn't allow creating a mock of a "final" class.  using uopz to remove the "final" keyword from the type.
    uopz_flags($loopType, null, 0);
    $loop = $this->getMockBuilder($loopType)->getMock();
    FileWatcherFactory::create($loop);
})->with([StreamSelectLoop::class, ExtLibevLoop::class, ExtLibeventLoop::class,ExtEventLoop::class])->throws(FileWatcherLoopNotSupported::class)->group("StreamSelect");

it('should throw FileWatcherLoopNotSupported when loop implementation is type of unknown LoopInterface',function () {
    $loop = $this->getMockBuilder(LoopInterface::class)->getMock();
    $fileWatcher = FileWatcherFactory::create($loop);
})->throws(FileWatcherLoopNotSupported::class);