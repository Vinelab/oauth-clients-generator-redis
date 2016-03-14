<?php

namespace Vinelab\ClientGenerator\Traits;

use Vinelab\Assistant\Generator;

/**
 * @author Kinane Domloje <kinane@vinelab.com>
 */
trait GeneratorTrait
{
    /**
     * Generate a V4 unique identifier
     *
     * @return string
     */
    public function generateUuid()
    {
        return (new Generator())->uuid();
    }
}
