<?php

namespace Church\Utils;

/**
 * Slug Utility.
 */
class Slug implements SlugInterface
{

    /**
    * {@inheritdoc}
     */
    public function create($text)
    {
        $slug = trim($text);
        $slug = mb_strtolower($slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace('.', '', $slug);

        return rawurlencode($slug);
    }
}
