<?php

namespace Polar\Annotation\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Polar\Annotation\Route;
use Polar\Annotation\Template;
use Polar\Middleware\AuthorizationMiddleware;
use Zend\Expressive\Router\Route as ZendRoute;

class PolarMetadata implements ClassMetadata
{

    private $name;

    private $route = null;

    private $template = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the fully-qualified class name of this persistent class.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function createRoute(Route $annotation)
    {
        $middlewares = [];
        if ($annotation->loginRequired) {
            $middlewares[] = AuthorizationMiddleware::class;
        }
        $middlewares[] = $this->getName();

        $route = new ZendRoute($annotation->path, $middlewares, $annotation->methods, $annotation->name);
        $route->setOptions($annotation->options);
        $this->route = $route;
    }

    public function hasRoute()
    {
        return $this->route instanceof ZendRoute;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function createTemplate(Template $template)
    {
        $this->template = $template->name;
    }

    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * Gets the mapped identifier field name.
     *
     * The returned structure is an array of the identifier field names.
     *
     * @return array
     */
    public function getIdentifier()
    {
        // TODO: Implement getIdentifier() method.
    }

    /**
     * Gets the ReflectionClass instance for this mapped class.
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass()
    {
        return new \ReflectionClass($this->getName());
    }

    /**
     * Checks if the given field name is a mapped identifier for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isIdentifier($fieldName)
    {
        // TODO: Implement isIdentifier() method.
    }

    /**
     * Checks if the given field is a mapped property for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasField($fieldName)
    {
        var_dump($this);
    }

    /**
     * Checks if the given field is a mapped association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function hasAssociation($fieldName)
    {
        // TODO: Implement hasAssociation() method.
    }

    /**
     * Checks if the given field is a mapped single valued association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isSingleValuedAssociation($fieldName)
    {
        // TODO: Implement isSingleValuedAssociation() method.
    }

    /**
     * Checks if the given field is a mapped collection valued association for this class.
     *
     * @param string $fieldName
     *
     * @return boolean
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        // TODO: Implement isCollectionValuedAssociation() method.
    }

    /**
     * A numerically indexed list of field names of this persistent class.
     *
     * This array includes identifier fields if present on this class.
     *
     * @return array
     */
    public function getFieldNames()
    {
        // TODO: Implement getFieldNames() method.
    }

    /**
     * Returns an array of identifier field names numerically indexed.
     *
     * @return array
     */
    public function getIdentifierFieldNames()
    {
        // TODO: Implement getIdentifierFieldNames() method.
    }

    /**
     * Returns a numerically indexed list of association names of this persistent class.
     *
     * This array includes identifier associations if present on this class.
     *
     * @return array
     */
    public function getAssociationNames()
    {
        // TODO: Implement getAssociationNames() method.
    }

    /**
     * Returns a type name of this field.
     *
     * This type names can be implementation specific but should at least include the php types:
     * integer, string, boolean, float/double, datetime.
     *
     * @param string $fieldName
     *
     * @return string
     */
    public function getTypeOfField($fieldName)
    {
        // TODO: Implement getTypeOfField() method.
    }

    /**
     * Returns the target class name of the given association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationTargetClass($assocName)
    {
        // TODO: Implement getAssociationTargetClass() method.
    }

    /**
     * Checks if the association is the inverse side of a bidirectional association.
     *
     * @param string $assocName
     *
     * @return boolean
     */
    public function isAssociationInverseSide($assocName)
    {
        // TODO: Implement isAssociationInverseSide() method.
    }

    /**
     * Returns the target field of the owning side of the association.
     *
     * @param string $assocName
     *
     * @return string
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        // TODO: Implement getAssociationMappedByTargetField() method.
    }

    /**
     * Returns the identifier of this object as an array with field name as key.
     *
     * Has to return an empty array if no identifier isset.
     *
     * @param object $object
     *
     * @return array
     */
    public function getIdentifierValues($object)
    {
        // TODO: Implement getIdentifierValues() method.
    }
}