<?php

namespace Polar\Action;

use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

abstract class AbstactAction implements MiddlewareInterface
{

    /**
     * @var TemplateRendererInterface
     */
    protected $view;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @return TemplateRendererInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param TemplateRendererInterface $view
     * @return $this
     */
    public function setView(TemplateRendererInterface $view, $templateName)
    {
        $this->view = $view;
        $this->templateName = $templateName;
        return $this;
    }

    public function render($data = [])
    {
        return new HtmlResponse($this->view->render($this->templateName, $data));
    }
}
