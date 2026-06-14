<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class NoCacheFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $this->setNoCacheHeaders(service('response'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $this->setNoCacheHeaders($response);

        return $response;
    }

    private function setNoCacheHeaders(ResponseInterface $response): void
    {
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }
}
