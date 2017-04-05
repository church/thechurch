<?php

namespace Church\Client\Mapzen;

use Church\Entity\Place\Place;

/**
 * Who's on First.
 */
interface WhosOnFirstInterface
{

    /**
     * Get a place by id.
     *
     * @param int $id
     */
    public function get(int $id) : Place;
}
