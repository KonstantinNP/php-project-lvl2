<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;
use function Differ\Differ\getName;
use function Differ\Differ\getValue1;
use function Differ\Differ\getValue2;
use function Differ\Differ\getMarker;
use function Differ\Differ\getChildren;

function genString($marker, $propertyArray, $value1, $value2): string
{
    if ($value1 != 'false' && $value1 != 'true' && $value1 != 'null' && !is_numeric($value1) && !is_object($value1)) {
        $value1 = "'$value1'";
    }
    if ($value2 != 'false' && $value2 != 'true' && $value2 != 'null' && !is_numeric($value2) && !is_object($value2)) {
        $value2 = "'$value2'";
    }
    if (is_object($value1)) {
        $value1 = "[complex value]";
    }
    if (is_object($value2)) {
        $value2 = "[complex value]";
    }
    $property = implode('.', $propertyArray);
    switch ($marker) {
        case "unchanged":
            $string = '';
            break;
        case "changed":
            $string = "Property '{$property}' was updated. From {$value1} to {$value2}";
            break;
        case "added":
            $string = "Property '{$property}' was added with value: {$value2}";
            break;
        case "deleted":
            $string = "Property '{$property}' was removed";
            break;
        default:
            throw new \Exception("Unknown Marker: " . $marker);
    }
    return $string;
}

function genProperty($diffTree, $acc = []): array
{
    $diffTree = is_object($diffTree) ? get_object_vars($diffTree) : $diffTree;
    $strings = [];
    foreach ($diffTree as $node) {
        $localAcc = $acc;
        if (!empty(getChildren($node))) {
            $acc[] = getName($node);
            $strings[] = genProperty(getChildren($node), $acc);
            $acc = $localAcc;
        } else {
            $localAcc[] = getName($node);
            $strings[] = genString(getMarker($node), $localAcc, getValue1($node), getValue2($node));
        }
    }
    return $strings;
}

function getPlainFormat($diffTree): string
{
    $strings = genProperty($diffTree);
    $strings = flattenAll($strings);
    $strings = array_filter($strings, fn($value) => $value != '');
    $result =  implode("\n", $strings);
    return "$result";
}
