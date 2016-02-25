<?php

namespace Polar\Route\Reader;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use Interop\Container\ContainerInterface;
use ReflectionClass;

class Annotations
{

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var  ContainerInterface
     */
    private $container;


    private $cache;

    /**
     * @return Reader
     */
    public function getReader()
    {
        if (!$this->reader) {
            $this->reader = new CachedReader(
                new AnnotationReader(),
                $this->getCache(),
                $this->container->get('config')['debug']
            );
        }
        return $this->reader;
    }

    public function getCache()
    {
        if ($this->cache) {
            return $this->cache;
        }
        return $this->cache = $this->container->get(Cache::class);
    }

    /**
     * RouteReader constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAnnotations()
    {
        $controllers = $this->container->get('config')['controllers'];
        foreach ($controllers as $controller) {
            $reflClass = new ReflectionClass($controller);
            $classAnnotations = $this->getReader()->getClassAnnotation($reflClass, 'Polar\Annotation\Route');
            $classAnnotations->middleware = $reflClass->getName();
            yield $classAnnotations;
        }
    }
}
