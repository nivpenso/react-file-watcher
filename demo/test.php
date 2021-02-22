<?php

require dirname(__DIR__).'/vendor/autoload.php';

use React\EventLoop\Factory;
use \ReactFileWatcher\FileWatcherFactory;
use \ReactFileWatcher\PathObjects\PathWatcher;

// create path to watch in the file system
$pathToWatchArr = [new PathWatcher('/tmp/', 1, ['txt'])];

// creating the loop using ReactPHP.
$loop = Factory::create();

// creating the file watcher based on the loop.
$fileWatcher = FileWatcherFactory::create($loop);

// waits for a signal to stop the loop
$loop->addSignal(9, function (int $signal) {
    print "Caught user interrupt signal: $signal" . PHP_EOL;
});

// call the watch and execute the callback when detecting change event.
$fsevent = $fileWatcher->Watch($pathToWatchArr, function($filename) {
    var_dump($filename);
    print PHP_EOL;
});

$loop->run();