<?php

namespace tests\Mocks;

use PHPUnit\Framework\TestCase;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

final class PromiseFulfillsWithTest extends TestCase
{
    /** @test */
    public function a_promise_fulfills(): void
    {
        $deferred = new Deferred();
        $deferred->resolve('bar');

        $this->assertPromiseFulfillsWith($deferred->promise(), 'bar');
    }

    /**
     * @param mixed $value
     */
    public function assertPromiseFulfillsWith(PromiseInterface $promise, $value): void
    {
        $promise->then($this->assertCallableCalledOnceWith($value));
    }

    /**
     * @param mixed $value
     */
    public function assertCallableCalledOnceWith($value): callable
    {
        $mock = $this->getMockBuilder(CallableStub::class)->getMock();
        $mock->expects($this->once())->method('__invoke')->with($value);

        return $mock;
    }
}

class CallableStub
{
    public function __invoke()
    {

    }
}
