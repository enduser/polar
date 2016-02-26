<?php

namespace Polar\Route\Reader;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as AbstractAnnotationDriver;
use ReflectionClass;

class Annotations extends AbstractAnnotationDriver
{
    /**
     * {@inheritDoc}
     */
    protected $entityAnnotationClasses = array(
        'Polar\Annotation\Route' => 1,
        'Polar\Annotation\Template' => 2,
    );


    public function getAnnotations()
    {
        $classes = $this->getAllClassNames();
        foreach ($classes as $controller) {
            $reflClass = new ReflectionClass($controller);
            $classAnnotations = $this->getReader()->getClassAnnotation($reflClass, 'Polar\Annotation\Route');
            $classAnnotations->middleware = $reflClass->getName();
            yield $classAnnotations;
        }
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        die($className);
    }


}
