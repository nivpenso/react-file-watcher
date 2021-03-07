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
    protected array $resourceWatchersToPathWatcher;
    protected $closure;

    public function __construct(LoopInterface $loop, int $timerInterval=3)
    {
        parent::__construct($loop);
        $this->timerInterval = $timerInterval;
        $this->closure = function() {
            // on initialization doing nothing.
        };
        $this->resourceWatchersToPathWatcher = [];
        $this->loop->addTimer($this->getTimerInterval(), [$this, 'onTimerTick']);
    }

    public function onTimerTick() {
        foreach ($this->resourceWatchersToPathWatcher as $map) {
            $changes = $map['watcher']->findChanges();
            if ($changes->hasChanges()) {
                foreach ($changes->getUpdatedFiles() as $updatedFile) {
                    $this->onChangeDetected($updatedFile, $map['pathWatcher'], $this->closure);
                }
                foreach ($changes->getDeletedFiles() as $deletedFile) {
                    $this->onChangeDetected($deletedFile, $map['pathWatcher'], $this->closure);
                }
                foreach ($changes->getNewFiles() as $newFile) {
                    $this->onChangeDetected($newFile, $map['pathWatcher'], $this->closure);
                }
            }
        }
        $this->loop->addTimer($this->timerInterval, [$this, 'onTimerTick']);
    }

    public function Watch(array $pathsToWatch, $closure)
    {
        $this->closure = $closure;
        array_map(function (PathWatcher $pathToWatch) use ($closure) {
            $watcher = new ResourceWatcher(new ResourceCacheMemory(), $this->convertPathWatcherToFinder($pathToWatch), new Crc32ContentHash());
            $watcher->initialize();
            $this->resourceWatchersToPathWatcher[] = ["watcher"=>$watcher, "pathWatcher" => $pathToWatch];
        }, $pathsToWatch);
    }

    protected function convertPathWatcherToFinder(PathWatcher $pathWatcher) : Finder {
        $finder = Finder::create();
        $dirPart = str_replace("\\", "/" , $pathWatcher->getDirPart());
        $finder->in($dirPart);
        if ($pathWatcher->isFile()) {
            $finder->name($pathWatcher->getFilenamePart());
        }
        return $finder;
    }

    /**
     * @return int
     */
    public function getTimerInterval(): int
    {
        return $this->timerInterval;
    }
}