<?php


namespace ReactFileWatcher\Exceptions;


use Throwable;

class WrongLoopImplementation extends \Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("This type of file watcher is not supported for that type of loop implementation. please use the factory to create the watcher. ", $code, $previous);
    }

}