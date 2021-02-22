<?php

namespace ReactFileWatcher;

interface FileWatcherInterface
{
    function Watch(array $pathsToWatch, $closure);
}