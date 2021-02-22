<?php

namespace ReactFileWatcher\Watchers;

use React\EventLoop\ExtUvLoop;
use React\EventLoop\LoopInterface;
use ReactFileWatcher\Exceptions\WrongLoopImplementation;

class LibUVFileWatcher extends AbstractFileWatcher
{
    protected $loopHandle;
    public function __construct(LoopInterface $loop)
    {
        parent::__construct($loop);
        if (get_class($this->loop) !== ExtUvLoop::class) {
            throw new WrongLoopImplementation();
        }
        $this->loopHandle = $this->loop->getUvLoop();
    }

    public function Watch(array $pathsToWatch, \Closure $closure)
    {
        array_map(function($path) use ($closure) {
            \uv_fs_event_init($this->loopHandle, $path, function($rsc, $name, $event, $status) use ($closure) {
                $closure([$rsc, $name, $event, $status]);
            }, 0);
        },  $pathsToWatch);
    }
}