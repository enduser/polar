<?php

namespace Polar\Annotation\Mapping\Driver;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver as AbstractAnnotationDriver;
use Polar\Annotation\Mapping\PolarMetadata;
use Polar\Annotation\Route;
use Polar\Annotation\Template;
use ReflectionClass;

class AnnotationDriver extends AbstractAnnotationDriver
{
    /**
     * {@inheritDoc}
     */
    protected $entityAnnotationClasses = array(
        'Polar\Annotation\Route' => 1,
        'Polar\Annotation\Template' => 2,
    );

    private $annotations = [];


    public function __construct($reader, $paths)
    {
        $this->annotations = new ArrayCollection();
        parent::__construct($reader, $paths);
    }

    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    private function collectAnnotations()
    {
        $classes = $this->getAllClassNames();
        foreach ($classes as $controller) {
            $metadata = new PolarMetadata($controller);
            $this->loadMetadataForClass($controller, $metadata);
            $this->annotations->set($controller, $metadata);
        }
    }


    /**
     * @return array|ArrayCollection
     */
    public function getAnnotations()
    {
        if ($this->annotations->isEmpty()) {
            $this->collectAnnotations();
        }
        return $this->annotations;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        /* @var $metadata \Polar\Annotation\Mapping\PolarMetadata */
        $class = $metadata->getReflectionClass();
        if ( ! $class) {
            // this happens when running annotation driver in combination with
            // static reflection services. This is not the nicest fix
            $class = new \ReflectionClass($metadata->getName());
        }

        $classAnnotations = $this->reader->getClassAnnotations($class);
        if ($classAnnotations) {
            foreach ($classAnnotations as $key => $annot) {
                if ( ! is_numeric($key)) {
                    continue;
                }

                $classAnnotations[get_class($annot)] = $annot;
            }
        }
        if (array_key_exists(Route::class, $classAnnotations)) {
            $metadata->createRoute($classAnnotations[Route::class]);
        }
        if (array_key_exists(Template::class, $classAnnotations)) {
            $metadata->createTemplate($classAnnotations[Template::class]);
        }
    }


}
