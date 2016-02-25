<?php

namespace Polar\Route;

use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Router\Exception;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

class AuraRouter implements RouterInterface
{
    /**
     * @var RouterContainer
     */
    private $container;


    public function __construct(RouterContainer $container = null)
    {
        if ($container === null) {
            $container = new RouterContainer();
        }
        $this->container = $container;
    }

    public function getMap()
    {
        return $this->container->getMap();
    }

    /**
     * Add a route.
     *
     * This method adds a route against which the underlying implementation may
     * match. Implementations MUST aggregate route instances, but MUST NOT use
     * the details to inject the underlying router until `match()` and/or
     * `generateUri()` is called.  This is required to allow consumers to
     * modify route instances before matching (e.g., to provide route options,
     * inject a name, etc.).
     *
     * The method MUST raise Exception\RuntimeException if called after either `match()`
     * or `generateUri()` have already been called, to ensure integrity of the
     * router between invocations of either of those methods.
     *
     * @param Route $route
     * @throws Exception\RuntimeException when called after match() or
     *     generateUri() have been called.
     */
    public function addRoute(Route $route)
    {
        $path      = $route->getPath();
        $auraRoute = $this->container->getMap()->route(
            $route->getName(),
            $path,
            $route->getMiddleware()
        );

        foreach ($route->getOptions() as $key => $value) {
            switch ($key) {
                case 'tokens':
                    $auraRoute->tokens($value);
                    break;
                case 'values':
                    $auraRoute->defaults($value);
                    break;
            }
        }

        $allowedMethods = $route->getAllowedMethods();
        if (Route::HTTP_METHOD_ANY === $allowedMethods) {
            return;
        }

        $auraRoute->allows($allowedMethods);
    }

    /**
     * Match a request against the known routes.
     *
     * Implementations will aggregate required information from the provided
     * request instance, and pass them to the underlying router implementation;
     * when done, they will then marshal a `RouteResult` instance indicating
     * the results of the matching operation and return it to the caller.
     *
     * @param  Request $request
     * @return RouteResult
     */
    public function match(Request $request)
    {
        $method = $request->getMethod();
        $params = $request->getServerParams();

        $params['REQUEST_METHOD'] = $method;
        $route  = $this->container->getMatcher()->match($request);
        if ($route === false) {
            $failedRoute = $this->container->getMatcher()->getFailedRoute();
            switch ($failedRoute->failedRule) {
                case 'Aura\Router\Rule\Allows':
                    return RouteResult::fromRouteFailure($failedRoute->allows);
                    break;
                case 'Aura\Router\Rule\Accepts':
                default:
                    return RouteResult::fromRouteFailure();
                    break;
            }
        }
        return RouteResult::fromRouteMatch(
            $route->name,
            $route->handler,
            $route->attributes
        );
    }

    /**
     * Generate a URI from the named route.
     *
     * Takes the named route and any substitutions, and attempts to generate a
     * URI from it.
     *
     * The URI generated MUST NOT be escaped. If you wish to escape any part of
     * the URI, this should be performed afterwards; consider passing the URI
     * to league/uri to encode it.
     *
     * @see https://github.com/auraphp/Aura.Router#generating-a-route-path
     * @see http://framework.zend.com/manual/current/en/modules/zend.mvc.routing.html
     * @param string $name
     * @param array $substitutions
     * @return string
     * @throws Exception\RuntimeException if unable to generate the given URI.
     */
    public function generateUri($name, array $substitutions = [])
    {
        return $this->container->getGenerator()->generate($name, $substitutions);
    }
}
