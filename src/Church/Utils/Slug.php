<?php

namespace Church\Utils;

use Patchwork\Utf8;

class Slug
{

    /**
     * Generate a Slug.
     *
     * @param string $text Text to be slugged.
     *
     * @return string Ready to use slug.
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
