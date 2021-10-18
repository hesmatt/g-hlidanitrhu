<?php

namespace Matt\SyGridBundle\Grid\Utils;

abstract class GridHelper
{
    /**
     * @param $source
     * @return string
     * Escapes the source, so that it replaces \ with : avoiding any unnecessary colisions in code structure
     */
    public static function escapeSourceClass($source): string
    {
        return str_replace('\\', ':', $source);
    }
}