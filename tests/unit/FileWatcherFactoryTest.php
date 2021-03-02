<?php

use React\EventLoop\LoopInterface;
use React\EventLoop\ExtEvLoop;
use \React\EventLoop\ExtUvLoop;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\FileWatcherFactory;
use ReactFileWatcher\Watchers\LibUVFileWatcher;
use ReactFileWatcher\Watchers\EVFileWatcher;

it('should return ExtUvFileWatcher when loop implementation is ExtUVLoop',function () {
    $loop = new ExtUvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(LibUVFileWatcher::class);
})->group("LibUV");

it('should return ExtEvFileWatcher when loop implementation is ExtEVLoop',function () {
    $loop = new ExtEvLoop();
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(EVFileWatcher::class);
})->group("LibUV");

it('should throw exception of file watcher not implemented when recieve not implemented LoopInterface type',function () {
    $loop = $this->getMockBuilder(LoopInterface::class)->getMock();
    /** @noinspection PhpParamsInspection */
    FileWatcherFactory::create($loop);
})->throws(FileWatcherLoopNotSupported::class);

it('should throw FileWatcherLoopNotSupported when loop implementation is type of unknown LoopInterface',function () {
    $loop = $this->getMockBuilder(LoopInterface::class)->getMock();
    /** @noinspection PhpParamsInspection */
    FileWatcherFactory::create($loop);
})->throws(FileWatcherLoopNotSupported::class);