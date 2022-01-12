<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getStylishFormat;
use function Differ\Formatters\Plain\getPlainFormat;

function getFormat($diff, $format)
{
    if ($format === "stylish") {
        return getStylishFormat($diff);
    }
    if ($format === "plain") {
        return getPlainFormat($diff);
    }
}
