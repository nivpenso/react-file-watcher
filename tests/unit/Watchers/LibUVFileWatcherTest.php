<?php

use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;
use ReactFileWatcher\Watchers\LibUVFileWatcher;

it('should throw WrongLoopImplementation exception when loop is not instance of ExtUvLoop', function() {
    $loop = $this->getMockBuilder(LoopInterface::class)->disableOriginalConstructor()->getMock();
    $fileWatcher = new LibUVFileWatcher($loop);
    $fileWatcher->Watch([],function() {});
})->throws(WrongLoopImplementation::class);

// TODO: test the functionality of the watch