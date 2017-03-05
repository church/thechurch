<?php

namespace Church\Utils;

/**
 * Slug Utility.
 */
class Slug
{

    /**
    * {@inheritdoc}
     */
    public static function create($text) : string
    {
        $slug = trim($text);
        $slug = mb_strtolower($slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace(['.', '(', ')'], '', $slug);
        $slug = preg_replace('/-{2,}/u', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
