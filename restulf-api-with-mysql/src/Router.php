<?php

namespace App;

use FastRoute\Dispatcher;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use function FastRoute\simpleDispatcher;

final class Router
{
    private $dispatcher;

    public function __construct(callable $routesDefinitionCallback)
    {
        $this->dispatcher = simpleDispatcher($routesDefinitionCallback);
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $routeInfo = $this->dispatcher->dispatch(
            $request->getMethod(), $request->getUri()->getPath()
        );

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(404, ['Content-Type' => 'text/plain'],  'Not found');
            case Dispatcher::FOUND:
                $params = $routeInfo[2] ?? [];
                return $routeInfo[1]($request, ... array_values($params));
        }

        throw new LogicException('Something went wrong in routing.');
    }
}
