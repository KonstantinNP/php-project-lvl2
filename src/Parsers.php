<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($type, $fileContent)
{
    if ($type === 'yaml' || $type === 'yml') {
        $value = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
    } elseif ($type === 'json') {
        $value = json_decode($fileContent, false);
    } else {
        throw new \Exception("'$type' invalid file extension");
    }
    return $value;
}
