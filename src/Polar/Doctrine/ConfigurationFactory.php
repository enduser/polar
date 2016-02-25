<?php

namespace Polar\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;

class ConfigurationFactory implements FactoryInterface
{


    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!array_key_exists('doctrine', $config)) {
            throw new \Exception('Doctrine configuration is missing!');
        }
        $cache = $container->get(Cache::class);
        $doctrine = new Configuration;
        $doctrine->setMetadataCacheImpl($cache);
        $doctrine->setQueryCacheImpl($cache);
        $doctrine->setProxyDir('data/Doctrine/Proxies');
        $doctrine->setProxyNamespace('Doctrine\Proxies');
        $doctrine->setAutoGenerateProxyClasses($container->get('config')['debug']);

        AnnotationRegistry::registerFile("vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
        $doctrine->setMetadataDriverImpl($this->getAnnotationDriver($container, $cache));

        return EntityManager::create($this->getConnection($container, $doctrine), $doctrine);
    }

    private function getAnnotationDriver(ContainerInterface $container, $cache)
    {
        $config = $container->get('config')['doctrine'];
        $paths = (array_key_exists('annotation_driver_paths', $config))?
            $config['annotation_driver_paths']: [];
        return new AnnotationDriver(
            new CachedReader(new AnnotationReader(), $cache),
            (array) $paths
        );
    }

    private function getConnection(ContainerInterface $container, $doctrine)
    {
        $config = $container->get('config')['doctrine'];
        if (!array_key_exists('connection', $config)) {
            trigger_error('Doctrine connection config is missing. Using default pdo_sqlite', E_USER_NOTICE);
        }
        $connectionOptions = array_key_exists('connection', $config)?
            $config['connection']: [
            'driver' => 'pdo_sqlite',
            'path' => 'data/database.sqlite'
            ];
        return DriverManager::getConnection($connectionOptions, $doctrine);
    }
}
