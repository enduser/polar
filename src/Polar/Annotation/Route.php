<?php

namespace Polar\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Route
 *
 * @Annotation
 * @Target("CLASS")
 */
class Route
{
    public $path;

    public $methods = ['GET'];

    public $name = null;

    public $options = [];
}
