<?php

namespace Polar\Action;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Polar\Annotation\Mapping\Driver\AnnotationDriver;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class ActionDelegator implements DelegatorFactoryInterface
{
    /**
     * @var TemplateRendererInterface
     */
    protected $view;

    /**
     * @var ContainerInterface
     */
    private $container;

    private function getView()
    {
        if ($this->view) {
            return $this->view;
        }
        return $this->view = $this->container->get(TemplateRendererInterface::class);
    }


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

        $this->container = $container;
        /** @var AbstactAction $action */
        $action   = call_user_func($callback);
        /** @var AnnotationDriver $reader */
        $reader = $container->get(AnnotationDriver::class);

        $view = $reader->getAnnotations()->filter(function($item) use($name){
           return $item->getName() == $name && $item->getTemplate() != null;
        });
        if (!$view->isEmpty()) {
            $action->setView($this->getView(), $view->first()->getTemplate());
        }
        return $action;
    }
}
