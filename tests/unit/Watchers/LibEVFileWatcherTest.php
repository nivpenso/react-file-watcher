<?php

use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;
use ReactFileWatcher\Watchers\EVFileWatcher;

it('should throw WrongLoopImplementation exception when loop is not instance of ExtEvLoop', function() {
    $loop = $this->getMockBuilder(LoopInterface::class)->disableOriginalConstructor()->getMock();
    $fileWatcher = new EVFileWatcher($loop);
    $fileWatcher->Watch([],function() {});
})->throws(WrongLoopImplementation::class);