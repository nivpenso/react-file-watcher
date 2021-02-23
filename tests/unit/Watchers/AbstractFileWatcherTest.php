<?php

use ReactFileWatcher\PathObjects\PathWatcher;
use ReactFileWatcher\Watchers\AbstractFileWatcher;

it('should return true when txt suffix is in filename', function($filename) {
    $fileWatcher = $this->getMockBuilder(AbstractFileWatcher::class)->disableOriginalConstructor()->getMock();
    $hasSuffix = callMethod($fileWatcher, 'isFilenameHasSuffix', [$filename, "txt"]);
    expect($hasSuffix)->toBeTrue();
})->with(['a.txt', '/tmp/a.txt', 'txta.txt', 'txt.txt.txt']);

it('should return false when txt suffix is not in filename', function($filename) {
    $fileWatcher = $this->getMockBuilder(AbstractFileWatcher::class)->disableOriginalConstructor()->getMock();
    $hasSuffix = callMethod($fileWatcher, 'isFilenameHasSuffix', [$filename, "txt"]);
    expect($hasSuffix)->toBeFalse();
})->with(['a', '/tmp/a', 'txta', 'a.txta']);

it('should call the closure when found a file changed that is not part of the exclude list', function() {
    $fileWatcher = $this->getMockBuilder(AbstractFileWatcher::class)->disableOriginalConstructor()->getMock();
    $shouldBeCalled = $this->getMockBuilder(stdClass::class)
        ->setMethods(['__invoke'])
        ->getMock();

    $shouldBeCalled->expects($this->once())
        ->method('__invoke');


    callMethod($fileWatcher, 'onChangeDetected', ["/tmp/myFile.txt", new PathWatcher("/tmp/", true, []) , $shouldBeCalled]);
});

it('should not call the closure when found a file changed which is part of the exclude list', function() {
    $fileWatcher = $this->getMockBuilder(AbstractFileWatcher::class)->disableOriginalConstructor()->getMock();
    $shouldBeCalled = $this->getMockBuilder(stdClass::class)
        ->setMethods(['__invoke'])
        ->getMock();

    $shouldBeCalled->expects($this->never())
        ->method('__invoke');


    callMethod($fileWatcher, 'onChangeDetected', ["/tmp/myFile.txt", new PathWatcher("/tmp/", true, ["txt"]) , $shouldBeCalled]);
});