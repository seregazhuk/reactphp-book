<?php

namespace tests\Mocks;

use PHPUnit\Framework\TestCase;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

final class PromiseRejectsTest extends TestCase
{
    /** @test */
    public function a_promise_rejects(): void
    {
        $deferred = new Deferred();
        $deferred->reject('value');

        $this->assertPromiseRejects($deferred->promise());
    }

    public function assertPromiseRejects(PromiseInterface $promise): void
    {
        $promise->then(null, $this->assertCallableCalledOnce());
    }

    public function assertCallableCalledOnce(): callable
    {
        $mock = $this->getMockBuilder(CallableStub::class)->getMock();
        $mock->expects($this->once())->method('__invoke');

        return $mock;
    }
}

class CallableStub
{
    public function __invoke()
    {

    }
}
