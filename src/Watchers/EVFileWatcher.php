<?php

namespace ReactFileWatcher\Watchers;

use React\EventLoop\ExtEvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\FileWatcherLoopNotSupported;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;
use ReactFileWatcher\PathObjects\PathWatcher;

class EVFileWatcher extends AbstractFileWatcher
{
    protected \EvLoop $loopHandle;

    public function __construct(LoopInterface $loop)
    {
        parent::__construct($loop);
        if ($this->loop instanceof ExtEvLoop) {
            $this->loopHandle = $this->getEvLoop($loop);
        }
        else {
            throw new WrongLoopImplementation();
        }
    }

    public function Watch(array $pathsToWatch, $closure)
    {
        return array_map(function(PathWatcher $path) use ($closure) {
            // set watcher on a file.
            if (!is_dir($path->getPathToWatch())) {
                $eventHandle = $this->setWatcherOnFile($path->getPathToWatch(), $path, $closure);
                return [$eventHandle];
            }

            // PathWatcher's path is directory, but recursive watch flag is off.
            // Set watchers for all the files within the dir since the Ev::Stat doesn't support single dir watch to detect changes on all dir's files.
            if (!$path->isRecursiveWatch()) {
                $filesAndDirs = scandir($path->getPathToWatch());
                $onlyFiles = array_filter($filesAndDirs, function ($item) { return !is_dir($item); } );
                return array_map(function($file) use ($path, $closure)  {
                    return $this->setWatcherOnFile($file, $path, $closure);
                }, $onlyFiles);
            }

            // PathWatcher's path is a directory and recursive watch flag is on.
            // Set watchers for all the files recursively.
            $directory = new \RecursiveDirectoryIterator($path->getPathToWatch());
            $dirFilter = new \RecursiveCallbackFilterIterator($directory, function ($current, $path, $iterator) {
                $fileName = $current->getFilename();
                // ext-ev doesn't support dir watch therefore, we must set a watcher for each file in the dir structure.
                return $fileName[0] !== '.';
            });
            $iterator = new \RecursiveIteratorIterator($dirFilter);
            $eventHandlesArr = [];
            foreach ($iterator as $info) {
                $eventHandle = $this->setWatcherOnFile($info->getPathname(), $path, $closure);
                array_push($eventHandlesArr, $eventHandle);
            }

            return $eventHandlesArr;
        },  $pathsToWatch);
    }

    /**
     * Since ExtEvLoop doesn't provide an access to the EvLoop instance, this function uses a reflection "hack" to get access to it.
     * @param ExtEvLoop $loopImpl
     * @return \EvLoop - the EvLoop instance of the given ExtEvLoop.
     */
    protected function getEvLoop(ExtEvLoop $loopImpl): \EvLoop {
        $classReflection = new \ReflectionClass(ExtEvLoop::class);
        $evLoopHandle = $classReflection->getProperty('loop');
        $evLoopHandle->setAccessible(true);
        return $evLoopHandle->getValue($loopImpl);
    }

    protected function setWatcherOnFile(string $filePath, PathWatcher $pathToWatch, $closure) {
        $eventHandle = $this->loopHandle->stat($filePath, 0.1, function ($handle) use ($pathToWatch, $closure) {
            $this->onChangeDetected($handle->path, $pathToWatch, $closure);
        });
        return $eventHandle;
    }
}