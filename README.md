# ReactPHP File Watcher

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
This package comes with tests in it. It uses the amazing package, [PEST](https://github.com/pestphp/pest). We suggest you to run the test with PEST, but you can also run in it with PHPUnit.

_**please note**_ - to run all tests out of the box you will have to install all the extensions on your environment. In case, you want to test only specific extension please execute a specific test-group.
#### running with pest
```
composer install
./vendor/bin/pest
```
#### running with phpunit
```
composer install
./vendor/bin/phpunit
```

