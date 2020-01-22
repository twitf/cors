<?php

namespace Twitf\Cors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Cors
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @Value("cors.allow_credentials")
     */
    private $allowCredentials;
    /**
     * @Value("cors.allow_origins")
     */
    private $allowOrigins;
    /**
     * @Value("cors.allow_methods")
     */
    private $allowMethods;
    /**
     * @Value("cors.allow_headers")
     */
    private $allowHeaders;
    /**
     * @Value("cors.expose_headers")
     */
    private $exposeHeaders;
    /**
     * @Value("cors.max_age")
     */
    private $maxAge;

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function addCorsHeaders(ResponseInterface $response)
    {
        if ($this->allowCredentials) {
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        $response->withHeader('Access-Control-Allow-Origin', $this->allowedOriginsToString());
        $response->withHeader('Access-Control-Expose-Headers', $this->toString($this->exposeHeaders));

        return $response;
    }

    public function addPreflightHeaders(ResponseInterface $response)
    {
        if ($this->allowCredentials) {
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        $response->withHeader('Access-Control-Allow-Methods', $this->toString($this->allowMethods));
        $response->withHeader('Access-Control-Allow-Headers', $this->toString($this->allowHeaders));
        $response->withHeader('Access-Control-Allow-Origin', $this->allowedOriginsToString());
        $response->withHeader('Access-Control-Max-Age', $this->maxAge);

        return $response;
    }

    public function isAllowed(): bool
    {
        if (!in_array($this->request->getMethod(), $this->allowMethods)) {
            return false;
        }

        if (in_array('*', $this->allowOrigins)) {
            return true;
        }

        $matches = collect($this->allowOrigins)->filter(function ($allowedOrigin) {
            return fnmatch($allowedOrigin, $this->request->header('Origin'));
        });

        return $matches->count() > 0;
    }

    protected function toString(array $array): string
    {
        return implode(', ', $array);
    }

    protected function allowedOriginsToString(): string
    {
        if (!$this->isAllowed()) {
            return '';
        }

        if (in_array('*', $this->allowOrigins) && !$this->allowCredentials) {
            return '*';
        }

        return $this->request->header('Origin');
    }
}
