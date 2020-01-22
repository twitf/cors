<?php

namespace Twitf\Cors\Middleware;

use Twitf\Cors\Cors;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{

    /**
     * @var Cors
     */
    protected $cors;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(Cors $cors, ResponseInterface $response)
    {
        $this->cors     = $cors;
        $this->response = $response;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isCorsRequest($request)) {
            return $handler->handle($request);
        }

        $this->cors->setRequest($request);

        if (!$this->cors->isAllowed()) {
            return $this->forbiddenResponse();
        }

        if ($this->isPreflightRequest($request)) {
            return $this->handlePreflightRequest();
        }

        $response = $handler->handle($request);
        return $this->cors->addCorsHeaders($response);
    }


    protected function isCorsRequest(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader('Origin')) {
            return false;
        }
        return $request->hasHeader('Origin') !== $request->url();
    }

    protected function isPreflightRequest(ServerRequestInterface $request): bool
    {
        return $request->getMethod() === 'OPTIONS';
    }

    protected function handlePreflightRequest()
    {
        if (!$this->cors->isAllowed()) {
            return $this->forbiddenResponse();
        }
        return $this->cors->addPreflightHeaders($this->response->withStatus(204));
    }

    protected function forbiddenResponse()
    {
        return $this->response->withStatus(403)->withBody(new SwooleStream('Forbidden (cors).'));
    }
}
