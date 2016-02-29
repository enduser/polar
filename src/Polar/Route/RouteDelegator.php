<?php

namespace Polar\Route;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Polar\Annotation\Mapping\Driver\AnnotationDriver;
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
        /** @var AnnotationDriver $reader */
        $reader = $container->get(AnnotationDriver::class);
        $routes = $reader->getAnnotations()->filter(function($item){
           return $item->hasRoute();
        });
        foreach ($routes as $route) {
            $router->addRoute($route->getRoute());
        }
        return $router;
    }
}
