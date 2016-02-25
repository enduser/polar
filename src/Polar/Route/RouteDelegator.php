<?php

namespace Polar\Route;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Expressive\Router\Route;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class RouteDelegator implements DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param  ContainerInterface $container
     * @param  string $name
     * @param  callable $callback
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var AuraRouter $router */
        $router = call_user_func($callback);
        /** @var Reader\Annotations $reader */
        $reader = $container->get(Reader\Annotations::class);
        foreach ($reader->getAnnotations() as $annotation) {
            $route = new Route($annotation->path, $annotation->middleware, $annotation->methods, $annotation->name);
            $route->setOptions($annotation->options);
            $router->addRoute($route);
        }
        return $router;
    }
}
