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
            if ($path->isRecursiveWatch()) {
                throw new RecursiveWatchNotImplemented();
                // TODO: implement recursive watch
            }
            // LibUV::uv_fs_event_init flags are not supported
            return uv_fs_event_init($this->loopHandle, $path->getPathToWatch(), function($eventResource, $fileName, $event, $status) use ($path, $closure) {
                $this->onChangeDetected($fileName, $path, $closure);
            });
        },  $pathsToWatch);
    }
}