<?php

use React\EventLoop\ExtEventLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtLibevLoop;
use React\EventLoop\LoopInterface;
use React\EventLoop\ExtEvLoop;
use \React\EventLoop\ExtUvLoop;
use React\EventLoop\StreamSelectLoop;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\FileWatcherFactory;
use ReactFileWatcher\Watchers\DefaultFileWatcher;
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
})->group("LibEV");

it('should return DefaultFileWatcher when no specific watcher implementation for the type of the loop implementation',function (LoopInterface $loop) {
    $fileWatcher = FileWatcherFactory::create($loop);
    expect(get_class($fileWatcher))->toBe(DefaultFileWatcher::class);
})->with([new ExtEventLoop(), new ExtLibeventLoop(), new StreamSelectLoop()]);