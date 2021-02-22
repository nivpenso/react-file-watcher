<?php

use React\EventLoop\LoopInterface;
use React\EventLoop\ExtEventLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtEvLoop;
use \React\EventLoop\ExtUvLoop;
use React\EventLoop\StreamSelectLoop;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\FileWatcherFactory;
use ReactFileWatcher\Watchers\LibUVFileWatcher;
use ReactFileWatcher\Watchers\LibEVFileWatcher;

it('should return ExtUvFileWatcher when loop implementation is ExtUVLoop',function () {
    $loop = new ExtUvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(LibUVFileWatcher::class);
})->group("LibUV");

it('should return ExtEvFileWatcher when loop implementation is ExtEVLoop',function () {
    $loop = new ExtEvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(LibEVFileWatcher::class);
})->group("LibUV");

// TODO: need to add into the require-dev of the composer all the ext-packages to allow this test to run.
it('should throw exception of file watcher not implemented',function ($loopType) {
    // phpunit doesn't allow creating a mock of a "final" class. Using UOPZ to remove the "final" keyword from the type.
    /** @noinspection PhpUndefinedFunctionInspection */
    uopz_flags($loopType, null, 0);
    $loop = $this->getMockBuilder($loopType)->getMock();
    /** @noinspection PhpParamsInspection */
    FileWatcherFactory::create($loop);
})->with([StreamSelectLoop::class, ExtLibeventLoop::class,ExtEventLoop::class])->throws(FileWatcherLoopNotSupported::class)->group("StreamSelect");

it('should throw FileWatcherLoopNotSupported when loop implementation is type of unknown LoopInterface',function () {
    $loop = $this->getMockBuilder(LoopInterface::class)->getMock();
    /** @noinspection PhpParamsInspection */
    FileWatcherFactory::create($loop);
})->throws(FileWatcherLoopNotSupported::class);