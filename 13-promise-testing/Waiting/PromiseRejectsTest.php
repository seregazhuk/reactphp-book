<?php

namespace tests\Waiting;

use Clue\React\Block;
use Exception;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

final class PromiseRejectsTest extends TestCase
{
    private const DEFAULT_TIMEOUT = 2;

    private LoopInterface $loop;

    protected function setUp(): void
    {
        $this->loop = Factory::create();
        parent::setUp();
    }

    /** @test */
    public function a_promise_rejects()
    {
        $deferred = new Deferred();
        $deferred->resolve();

        $this->assertPromiseRejects($deferred->promise());
    }


    /**
     * @param PromiseInterface $promise
     * @param int|null $timeout seconds to wait for resolving
     * @return mixed
     */
    public function assertPromiseRejects(PromiseInterface $promise, $timeout = null)
    {
        try {
            $this->addToAssertionCount(1);
            Block\await($promise, $this->loop, $timeout ? : self::DEFAULT_TIMEOUT);
        } catch (Exception $exception) {
            return $exception;
        }
        $this->fail('Failed asserting that promise rejects. Promise was fulfilled.');
    }
}

