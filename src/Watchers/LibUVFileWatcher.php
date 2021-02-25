<?php

namespace ReactFileWatcher\Watchers;

use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\RecursiveWatchNotImplemented;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;
use ReactFileWatcher\PathObjects\PathWatcher;
use function uv_fs_event_init;

class LibUVFileWatcher extends AbstractFileWatcher
{
    protected $loopHandle;
    public function __construct(LoopInterface $loop)
    {
        parent::__construct($loop);
        if (!($this->loop instanceof ExtUvLoop)) {
            throw new WrongLoopImplementation();
        }
        $this->loopHandle = $this->loop->getUvLoop();
    }

    public function Watch(array $pathsToWatch, $closure)
    {
        return array_map(function(PathWatcher $path) use ($closure) {
            $eventHandlesArr = [];
            // LibUV::uv_fs_event_init flags are not supported
            $eventHandle = uv_fs_event_init($this->loopHandle, $path->getPathToWatch(), function($eventResource, $fileName, $event, $status) use ($path, $closure) {
                $this->onChangeDetected($fileName, $path, $closure);
            });
            array_push($eventHandlesArr, $eventHandle);

            if ($path->isRecursiveWatch() && is_dir($path->getPathToWatch())) {
                $directory = new \RecursiveDirectoryIterator($path->getPathToWatch());
                $dirFilter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
                    // Skip hidden files and directories.
                    if ($current->getFilename()[0] === '.') {
                        return false;
                    }
                    if ($current->isDir()) {
                        // Only recurse into intended subdirectories.
                        return true;
                    }
                    return false;
                });
                $iterator = new \RecursiveIteratorIterator($dirFilter, \RecursiveIteratorIterator::SELF_FIRST);
                foreach ($iterator as $info) {
                    // LibUV::uv_fs_event_init flags are not supported
                    $eventHandle =  uv_fs_event_init($this->loopHandle, $info->getPathname(), function($eventResource, $fileName, $event, $status) use ($path, $closure) {
                        $this->onChangeDetected($fileName, $path, $closure);
                    });
                    array_push($eventHandlesArr, $eventHandle);
                }
            }
            return $eventHandlesArr;
        },  $pathsToWatch);
    }
}