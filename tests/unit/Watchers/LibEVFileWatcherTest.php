<?php

use React\EventLoop\ExtEvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;
use ReactFileWatcher\Watchers\LibEVFileWatcher;

it('should throw WrongLoopImplementation exception when loop is not instance of ExtEvLoop', function() {
    $loop = $this->getMockBuilder(LoopInterface::class)->disableOriginalConstructor()->getMock();
    $fileWatcher = new LibEVFileWatcher($loop);
    $fileWatcher->Watch([],function() {});
})->throws(WrongLoopImplementation::class);

it('should throw FileWatcherLoopNotSupported exception when calling watch', function() {
    $loop = $this->getMockBuilder(ExtEvLoop::class)->disableOriginalConstructor()->getMock();
    $fileWatcher = new LibEVFileWatcher($loop);
    $fileWatcher->Watch([],function() {});
})->throws(FileWatcherLoopNotSupported::class);