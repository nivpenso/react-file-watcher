<?php

namespace ReactFileWatcher\Exceptions;

class FileWatcherLoopNotSupported extends \Exception
{
    public function __construct($code = 0, \Throwable $previous = null)
    {
        parent::__construct("The FileWatcher for this loop implementation is not supported yet", $code, $previous);
    }

}