<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use React\EventLoop\LoopInterface;

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * @param $object
 * @param string $method
 * @param array $parameters
 * @return mixed
 * @throws \Exception
 */
function callMethod($object, string $method , array $parameters = [])
{
    try {
        $className = get_class($object);
        $reflection = new \ReflectionClass($className);
    }
    catch (\ReflectionException $e) {
        throw new \Exception($e->getMessage());
    }

    $method = $reflection->getMethod($method);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
}

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) {
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

function createStopLoopCallbackAfterFileChanged($that, InvocationOrder $invocationOrder , LoopInterface $loop, $conditionToCheck) {
    $shouldBeCalled = $that->getMockBuilder(stdClass::class)
        ->setMethods(['__invoke'])
        ->getMock();

    $shouldBeCalled->expects($invocationOrder)
        ->method('__invoke')->will($that->returnCallback(function($filename) use ($loop, $conditionToCheck) {
            $conditionToCheck($filename);
            $loop->stop();
        }));
    return $shouldBeCalled;
}

const TEMP_DIR = "/tmp/react-watcher-tests";