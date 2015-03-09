<?php

namespace Church\Utils;

use Patchwork\Utf8;

class Slug
{

    protected $utf8;

    public function __construct(Utf8 $utf8)
    {
        $this->utf8 = $utf8;
    }

    /**
     * Get Utf8
     *
     * @return Utf8
     */
    public function getUtf8()
    {
        return $this->utf8;
    }

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
        $slug = $this->getUtf8()->strtolower($slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace('.', '', $slug);
        $slug = str_replace('ʻ', '', $slug);

        return $this->getUtf8()->toAscii($slug);
    }
}
