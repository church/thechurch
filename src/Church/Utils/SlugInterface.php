<?php

namespace Church\Utils;

/**
 * Slub.
 */
interface SlugInterface
{
    /**
     * Generate a Slug.
     *
     * @param string $text Text to be slugged.
     */
    public function create($text) : string;
}
