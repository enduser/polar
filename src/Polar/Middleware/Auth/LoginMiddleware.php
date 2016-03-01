<?php

namespace Polar\Middleware\Auth;

use Polar\Annotation as Polar;
use Polar\Middleware\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
/**
 * Class LoginMiddleware
 * @package Polar\Middleware\Auth
 *
 * @Polar\Route(path="/admin/login", methods={"GET", "POST"}, name="admin.login")
 * @Polar\Template(name="admin::dashboard")
 */
class LoginMiddleware extends AbstractMiddleware
{

    /**
     * Process an incoming request and/or response.
     *
     * Accepts a server-side request and a response instance, and does
     * something with them.
     *
     * If the response is not complete and/or further processing would not
     * interfere with the work done in the middleware, or if the middleware
     * wants to delegate to another process, it can use the `$out` callable
     * if present.
     *
     * If the middleware does not return a value, execution of the current
     * request is considered complete, and the response instance provided will
     * be considered the response to return.
     *
     * Alternately, the middleware may return a response instance.
     *
     * Often, middleware will `return $out();`, with the assumption that a
     * later middleware will return a response.
     *
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
       return $this->render(['message' => "Admin Panel"]);
    }
}