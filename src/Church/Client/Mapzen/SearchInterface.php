<?php

namespace Church\Client\Mapzen;

use Church\Entity\Location;

/**
 * Executing a Search on Mapzen.
 */
interface SearchInterface
{

    /**
     * Get a place by id.
     *
     * @param string $id
     */
    public function get(string $id) : Location;
}
