<?php

namespace Polar\Middleware;

use Interop\Container\ContainerInterface;
use Polar\Annotation\Mapping\Driver\AnnotationDriver;
use Polar\Annotation\Template;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

/**
 * Class AbstractMiddleware
 * @package Polar\Middleware
 */
abstract class AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * AbstractMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return \ReflectionProperty
     * @throws \Exception
     */
    private function getTemplateName()
    {
        $reflectionClass = new \ReflectionClass($this);
        if (array_key_exists($reflectionClass->getName(), $this->container->get('config')['polar']['templates'])) {
            return $this->container->get('config')['polar']['templates'][$reflectionClass->getName()];
        }
        /** @var AnnotationDriver $reader */
        $reader = $this->container->get(AnnotationDriver::class);
        if ($reflectionClass->hasProperty('templateName')) {
            return $reflectionClass->getProperty('templateName')->getValue($this);
        }
        $template = $reader->getReader()->getClassAnnotation($reflectionClass, Template::class);
        if ($template instanceof Template) {
            return $template->name;
        }
        throw new \Exception('Template name is not configured');
    }

    /**
     * @param array $data
     * @param null $templateName
     * @return HtmlResponse
     * @throws \Exception
     */
    public function render(array $data, $templateName = null)
    {
        /** @var TemplateRendererInterface $template */
        $template = $this->container->get(TemplateRendererInterface::class);
        $name = ($templateName)?:$this->getTemplateName();
        return new HtmlResponse($template->render($name, $data));
    }
}
