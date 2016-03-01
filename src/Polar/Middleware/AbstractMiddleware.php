<?php

namespace Polar\Middleware;

use Interop\Container\ContainerInterface;
use Polar\Annotation\Mapping\Driver\AnnotationDriver;
use Polar\Annotation\Template;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function getTemplateName()
    {
        /** @var AnnotationDriver $reader */
        $reader = $this->container->get(AnnotationDriver::class);
        $reflectionClass = new \ReflectionClass($this);
        if ($reflectionClass->hasProperty('templateName')) {
            return $reflectionClass->getProperty('templateName');
        }
        $template = $reader->getReader()->getClassAnnotation($reflectionClass, Template::class);
        if ($template instanceof Template) {
           return $template->name;
        }
        //TODO get template from config
        throw new \Exception('Template not found');
    }

    public function render(array $data)
    {
        /** @var TemplateRendererInterface $template */
        $template = $this->container->get(TemplateRendererInterface::class);
        return new HtmlResponse($template->render($this->getTemplateName(), $data));
    }

}