<?php

namespace Polar;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use Zend\Expressive\Router\RouterInterface;

class ModuleConfig
{

    public function __invoke()
    {
        AnnotationRegistry::registerFile(__DIR__ . DIRECTORY_SEPARATOR. "Annotation/Annotations.php");
        return [
            'polar' => [
                'annotations' => [
                    'middleware' => [

                    ],
                ]
            ],
            'dependencies' => [
                'factories' => [
                    RouterInterface::class => Route\RouteFactory::class,
                    Route\Reader\Annotations::class => Route\Reader\AnnotationFactory::class,
                    EntityManager::class => Doctrine\ConfigurationFactory::class,
                    Cache::class => Doctrine\CacheFactory::class
                ],
                'delegators' => [
                    RouterInterface::class => [
                        Route\RouteDelegator::class
                    ]
                ],
            ],
        ];
    }
}
