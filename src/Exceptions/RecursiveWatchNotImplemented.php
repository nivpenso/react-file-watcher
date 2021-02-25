<?php


namespace ReactFileWatcher\Exceptions;


use Throwable;

class RecursiveWatchNotImplemented extends \Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("recursive watch has not implemented yet", $code, $previous);
    }

}