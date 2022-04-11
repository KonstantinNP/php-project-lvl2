<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\getJsonFormat;
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
    if ($format === "json") {
        return getJsonFormat($diff);
    }
}
