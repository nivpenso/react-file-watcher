# ReactPHP File Watcher

[![react-watcher-php](https://circleci.com/gh/nivpenso/react-file-watcher.svg?style=svg)](https://app.circleci.com/pipelines/github/nivpenso/react-file-watcher)

### About
This package is based on [reactphp/event-loop](https://github.com/reactphp/event-loop) and provides an easy-to-use interface to set filesystem watchers and detect changes.

### More in depth

The package is utilizing the abstraction of react-php to create an efficient file-watcher using the underlying platform package that is installed on the environment

### Support
| Extension     | Status        | Will be supported?  |
| ------------- |:-------------:| -----:|
| ext-uv        | Supported 
| ext-libevent  | Not Supported | Yes |
| ext-ev        | Not Supported | Yes |
| ext-event     | Not Supported | Yes |
| ext-libev     | Not Supported | No  |


### How to install
```
composer require nivpenso/react-file-watcher
```


### How to use
#### code snippet
```php
use React\EventLoop\Factory;
use ReactFileWatcher\FileWatcherFactory;
use ReactFileWatcher\PathObjects\PathWatcher;

// create path to watch in the file system
$pathToWatch = new PathWatcher("/tmp/", 1, []);

// creating the loop using ReactPHP.
$loop = Factory::create();

// creating the file watcher based on the loop.
$fileWatcher = FileWatcherFactory::create($loop);

// call the watch and execute the callback when detecting change event.
$fsevent = $fileWatcher->Watch([$pathToWatch], function($filename) {
    var_dump($filename);
    print PHP_EOL;
});
```

### Demo
This package comes with a demo that can be used. feel free to run it
#### Running the demo on local (Linux)
1. make sure one of the supported libraries is installed on your environment (suggested: `ext-uv`)
2. run the following commands
```
# install dependecies
composer install --no-dev
# run the demo
php ./demo/test.php
```
After the process started you can start changing files under the path to watch (default: `/tmp`) and see the messages in the terminal.  
#### Running the demo on Docker
In case you don't have a linux machine ready you can use Docker to test it.
**Docker file will be provided in the future**

### Testing
This package comes with tests in it. It uses the amazing package, [PEST](https://github.com/pestphp/pest). You can either run the tests on local or use the provided docker file that already contains the installation of `ev`, `uv`, `libevent`, `event` extensions.

#### running with pest
```
composer install
./vendor/bin/pest
```
