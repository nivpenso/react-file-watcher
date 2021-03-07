<?php


namespace ReactFileWatcher\Exceptions;


use Exception;
use Throwable;

class PathDoesntExist extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("the path doesn't exist", $code, $previous);
    }

}