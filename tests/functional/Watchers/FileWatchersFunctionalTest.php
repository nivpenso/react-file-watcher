<?php

use React\EventLoop\ExtEventLoop;
use React\EventLoop\ExtEvLoop;
use React\EventLoop\ExtLibeventLoop;
use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use ReactFileWatcher\FileWatcherFactory;
use ReactFileWatcher\PathObjects\PathWatcher;
use function PHPUnit\Framework\MockObject\MockBuilder;

beforeEach(function() {
    // make sure that /tmp/react-watcher-tests dir exists
    if (!is_dir(TEMP_DIR)) {
        if (is_file(TEMP_DIR)) {
            throw new Exception("found a file in /tmp folder with the same name of the directory name for these tests ". TEMP_DIR);
        }
        // create the folder
        mkdir(TEMP_DIR);
        return;
    }
    // make sure that /tmp/react-watcher-tests dir is empty
    recursiveRemoveDirectory(TEMP_DIR);
    mkdir(TEMP_DIR);
});

afterAll(function() {
    // make sure that /tmp/react-watcher-tests dir is empty
    recursiveRemoveDirectory(TEMP_DIR);
});

it("should watch for changes on recursive path watch", function(LoopInterface $loop) {
    // prepare file with first content.
    $tempFirstFileName = "1";
    $tempFirstFilePath = TEMP_DIR. "/$tempFirstFileName";
    file_put_contents($tempFirstFilePath, "first file: first insert");

    $newDir = "newDir";
    mkdir(TEMP_DIR."/$newDir");
    $tempSecondFileName = "newdir-1";
    $tempSecondFilePath = TEMP_DIR . "/$newDir/$tempSecondFileName";
    file_put_contents($tempSecondFilePath, "second file: first insert");

    $tempThirdFileName = "newdir-2";
    $tempThirdFilePath = TEMP_DIR . "/$newDir/$tempThirdFileName";
    file_put_contents($tempThirdFilePath, "third file: first insert");

    // prepare the event loop, the watcher and the path to watch
    $watcher = FileWatcherFactory::create($loop);
    $pathWatcher = new PathWatcher(TEMP_DIR, true, []);

    // prepare a "mock" callback that will check that it has been fired exactly 3 times for file changed events
    $bothFileNames = [$tempFirstFilePath, $tempSecondFilePath, $tempThirdFilePath];
    $shouldBeCalled = createStopLoopCallbackAfterFileChanged($this, $this->exactly(3), $loop, function($filename) use ($bothFileNames) {
        // the name of the changed file provided in the callback arg is right.
        expect($bothFileNames)->toContain($filename);
    });

    // set the watcher to watch the path. the unused variable $fsEvents is critical because without it the watcher won't work.
    $fsEvents = $watcher->Watch([$pathWatcher], $shouldBeCalled);
    // add a timer that will keep the loop running until the loop is stopped manually or until a timeout (interval)
    $loop->addTimer(5, function() use ($loop){
        $loop->stop();
    });

    // set a future callback to be called right after the loop starts - this is used to write something to the filesystem after the loop starts.
    $loop->futureTick(function () use ($loop, $tempFirstFilePath, $tempSecondFilePath, $tempThirdFilePath) {
        file_put_contents($tempFirstFilePath, "first file: second insert");
        file_put_contents($tempSecondFilePath, "second file: second insert");
        file_put_contents($tempThirdFilePath, "third file: second insert");
    });

    $loop->run();
})->with([new ExtUvLoop(), new ExtEvLoop(), new StreamSelectLoop(), new ExtEventLoop(), new ExtLibeventLoop()]);

it("should invoke closure when file has modified", function(LoopInterface $loop) {
    // prepare file with first content.
    $tempFileName = "1";
    $tempFilePath = TEMP_DIR. "/$tempFileName";
    file_put_contents($tempFilePath, "first insert");

    // prepare the event loop, the watcher and the path to watch
    $watcher = FileWatcherFactory::create($loop);
    $pathWatcher = new PathWatcher($tempFilePath, false, []);

    // prepare a "mock" callback that will check that it has been fired one time on event of file change.
    $shouldBeCalled = createStopLoopCallbackAfterFileChanged($this, $this->once(), $loop, function($filename) use ($tempFilePath) {
        // the name of the changed file provided in the callback arg is right.
        expect($filename)->toBe($tempFilePath);
    });

    // set the watcher to watch the path. the unused variable $fsEvents is critical because without it the watcher won't work.
    $fsEvents = $watcher->Watch([$pathWatcher], $shouldBeCalled);
    // add a timer that will keep the loop running until the loop is stopped manually or until a timeout (interval)
    $loop->addTimer(5, function() use ($loop){
        $loop->stop();
    });

    // set a future callback to be called right after the loop starts - this is used to write something to the filesystem after the loop starts.
    $loop->futureTick(function () use ($tempFilePath) {
        file_put_contents($tempFilePath, "second insert");
    });

    $loop->run();
})->with([new ExtUvLoop(), new ExtEvLoop(), new StreamSelectLoop(), new ExtEventLoop(), new ExtLibeventLoop()]);

it("should not invoke closure when file has modified but is part of the ignore suffix list", function(LoopInterface $loop) {
    // prepare file with first content.
    $tempFileName = "1.txt";
    $tempFilePath = TEMP_DIR. "/$tempFileName";
    file_put_contents($tempFilePath, "first insert");

    // prepare the event loop, the watcher and the path to watch
    $watcher = FileWatcherFactory::create($loop);
    $pathWatcher = new PathWatcher(TEMP_DIR, false, ["txt"]);

    // prepare a "mock" callback that check that it will never be called.
    $shouldBeCalled = createStopLoopCallbackAfterFileChanged($this, $this->never(), $loop, function($filename) {
    });

    // set the watcher to watch the path. the unused variable $fsEvents is critical because without it the watcher won't work.
    $fsEvents = $watcher->Watch([$pathWatcher], $shouldBeCalled);
    // add a timer that will keep the loop running until the loop is stopped manually or until a timeout (interval)
    $loop->addTimer(1, function() use ($loop){
        $loop->stop();
    });

    // set a future callback to be called right after the loop starts - this is used to write something to the filesystem after the loop starts.
    $loop->futureTick(function () use ($tempFilePath) {
        file_put_contents($tempFilePath, "second insert");
    });

    $loop->run();
})->with([new ExtUvLoop(), new ExtEvLoop(), new StreamSelectLoop(), new ExtEventLoop(), new ExtLibeventLoop()]);

it("should invoke closure twice when 2 files were modified for the same PathWatcher", function(LoopInterface $loop) {
    // prepare files with first content.
    $firstTempFileName = "1";
    $firstTempPath = TEMP_DIR."/$firstTempFileName";
    $secondTempFileName = "2";
    $secondTempPath = TEMP_DIR."/$secondTempFileName";
    file_put_contents($firstTempPath, "first file: first insert");
    file_put_contents($secondTempPath, "second file: first insert");

    // prepare the event loop, the watcher and the path to watch
    $watcher = FileWatcherFactory::create($loop);
    $pathWatcher = new PathWatcher(TEMP_DIR, false, []);

    // prepare a "mock" callback that will check that it has been fired exactly 2 times for file changed events
    $bothFileNames = [$firstTempPath, $secondTempPath];
    $shouldBeCalled = createStopLoopCallbackAfterFileChanged($this, $this->exactly(2), $loop, function($filename) use ($bothFileNames) {
        // the name of the changed file provided in the callback arg is right.
        expect($bothFileNames)->toContain($filename);
    });

    // set the watcher to watch the path. the unused variable $fsEvents is critical because without it the watcher won't work.
    $fsEvents = $watcher->Watch([$pathWatcher], $shouldBeCalled);
    // add a timer that will keep the loop running until the loop is stopped manually or until a timeout (interval)
    $loop->addTimer(5, function() use ($loop){
        $loop->stop();
    });

    // set a future callback to be called right after the loop starts - this is used to write something to the filesystem after the loop starts.
    $loop->futureTick(function () use ($loop, $firstTempFileName, $secondTempFileName) {
        file_put_contents(TEMP_DIR."/$firstTempFileName", "first file: second insert");
        file_put_contents(TEMP_DIR."/$secondTempFileName", "second file: second insert");
    });

    $loop->run();
})->with([new ExtUvLoop(), new ExtEvLoop(), new StreamSelectLoop(), new ExtEventLoop(), new ExtLibeventLoop()]);

it("should not invoke closure when path has not change", function(LoopInterface $loop) {
    // prepare files with first content.
    $firstTempFileName = "1";
    $secondTempFileName = "2";
    file_put_contents(TEMP_DIR."/$firstTempFileName", "first file: first insert");
    file_put_contents(TEMP_DIR."/$secondTempFileName", "second file: first insert");

    // prepare the event loop, the watcher and the path to watch
    $watcher = FileWatcherFactory::create($loop);
    $pathWatcher = new PathWatcher(TEMP_DIR."/$firstTempFileName", false, []);

    // prepare a "mock" callback that will check that it has been fired exactly 2 times for file changed events
    $shouldBeCalled = createStopLoopCallbackAfterFileChanged($this, $this->never(), $loop, function($filename) {
    });

    // set the watcher to watch the path. the unused variable $fsEvents is critical because without it the watcher won't work.
    $fsEvents = $watcher->Watch([$pathWatcher], $shouldBeCalled);
    // add a timer that will keep the loop running until the loop is stopped manually or until a timeout (interval)
    $loop->addTimer(1, function() use ($loop){
        $loop->stop();
    });

    // set a future callback to be called right after the loop starts - this is used to write something to the filesystem after the loop starts.
    $loop->futureTick(function () use ($loop, $firstTempFileName, $secondTempFileName) {
        file_put_contents(TEMP_DIR."/$secondTempFileName", "second file: second insert");
    });

    $loop->run();
})->with([new ExtUvLoop(), new ExtEvLoop(), new StreamSelectLoop(), new ExtEventLoop(), new ExtLibeventLoop()]);

it("should invoke closure twice when 2 files were modified for the 2 different PathWatchers", function(LoopInterface $loop) {
    // prepare files with first content.
    $firstTempFileName = "1";
    $secondTempFileName = "2";
    $firstTempPath = TEMP_DIR."/$firstTempFileName";
    $secondTempPath = TEMP_DIR."/$secondTempFileName";
    file_put_contents($firstTempPath, "first file: first insert");
    file_put_contents($secondTempPath, "second file: first insert");

    // prepare the event loop, the watcher and the path to watch
    $watcher = FileWatcherFactory::create($loop);
    $pathWatcher1 = new PathWatcher($firstTempPath, false, []);
    $pathWatcher2 = new PathWatcher($secondTempPath, false, []);

    // prepare a "mock" callback that will check that it has been fired exactly 2 times for file changed events
    $bothFileNames = [TEMP_DIR."/$firstTempFileName", TEMP_DIR."/$secondTempFileName"];
    $shouldBeCalled = createStopLoopCallbackAfterFileChanged($this, $this->exactly(2), $loop, function($filename) use ($bothFileNames) {
        // the name of the changed file provided in the callback arg is right.
        expect($bothFileNames)->toContain($filename);
    });

    // set the watcher to watch the path. the unused variable $fsEvents is critical because without it the watcher won't work.
    $fsEvents = $watcher->Watch([$pathWatcher1, $pathWatcher2], $shouldBeCalled);
    // add a timer that will keep the loop running until the loop is stopped manually or until a timeout (interval)
    $loop->addTimer(5, function() use ($loop){
        $loop->stop();
    });

    // set a future callback to be called right after the loop starts - this is used to write something to the filesystem after the loop starts.
    $loop->futureTick(function () use ($loop, $firstTempFileName, $secondTempFileName) {
        file_put_contents(TEMP_DIR."/$firstTempFileName", "first file: second insert");
        file_put_contents(TEMP_DIR."/$secondTempFileName", "second file: second insert");
    });

    $loop->run();
})->with([new ExtUvLoop(), new ExtEvLoop(), new StreamSelectLoop(), new ExtEventLoop(), new ExtLibeventLoop()]);

// TODO: test the functionality of the watch