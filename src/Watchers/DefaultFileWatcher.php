<?php


namespace ReactFileWatcher\Watchers;


use React\EventLoop\LoopInterface;
use ReactFileWatcher\PathObjects\PathWatcher;
use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\ResourceWatcher\ResourceWatcher;

class DefaultFileWatcher extends AbstractFileWatcher
{
    protected int $timerInterval;

    public function __construct(LoopInterface $loop, int $timerInterval=3)
    {
        parent::__construct($loop);
        $this->timerInterval = $timerInterval;
    }

    public function Watch(array $pathsToWatch, $closure)
    {
        array_map(function (PathWatcher $pathToWatch) use ($closure) {
            $watcher = new ResourceWatcher(new ResourceCacheMemory(), $this->convertPathWatcherToFinder($pathToWatch), new Crc32ContentHash());
            $this->loop->addTimer($this->getTimerInterval(), function() use ($pathToWatch, $closure, $watcher){
                $changes = $watcher->findChanges();
                if ($changes->hasChanges()) {
                    // TODO: pass to the closure the files that were changed.
                    $closure();
                }
            });
        }, $pathsToWatch);
        // TODO: Implement Watch() method.
    }

    protected function convertPathWatcherToFinder(PathWatcher $pathWatcher) : Finder{
        throw new \Exception("not implemented");
    }

    /**
     * @return int
     */
    public function getTimerInterval(): int
    {
        return $this->timerInterval;
    }
}