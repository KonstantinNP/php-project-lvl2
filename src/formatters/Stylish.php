<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;
use function Differ\Differ\getName;
use function Differ\Differ\getValue1;
use function Differ\Differ\getValue2;
use function Differ\Differ\getMarker;
use function Differ\Differ\getChildren;

const INDENT_STEP = 4;
const MARKER_PLACE = 2;

function genTree($tree, $deep): string
{
    $indent = INDENT_STEP * $deep;
    $tree = get_object_vars($tree);
    $keys = array_keys($tree);
    $result = array_map(function ($key) use ($tree, $deep, $indent) {
        if (is_object($tree[$key])) {
            return str_repeat(" ", $indent) . "$key: " . genTree($tree[$key], ++$deep);
        }
        return str_repeat(" ", $indent) . "$key: $tree[$key]";
    }, $keys);
    $result = flattenAll($result);
    return "{\n" . implode("\n", $result) .  "\n" . str_repeat(" ", $indent - INDENT_STEP) . "}";
}

function generateString($diffTree, $deep): string
{
    $indent = INDENT_STEP * $deep - MARKER_PLACE;
    $diffTree = is_object($diffTree) ? get_object_vars($diffTree) : $diffTree;
    $result = array_map(function ($node) use ($deep, $indent) {
        if (!empty(getChildren($node))) {
            return str_repeat(" ", $indent) . "  " . getName($node) . ": {\n" .
                generateString(getChildren($node), ++$deep) . "\n" . str_repeat(" ", $indent) . "  }";
        }
        $value1 = is_object(getValue1($node)) ? genTree(getValue1($node), ++$deep) : getValue1($node);
        $value2 = is_object(getValue2($node)) ? genTree(getValue2($node), ++$deep) : getValue2($node);
        $value1 = (($value1 === "") || (is_null($value1))) ? ": " : ": $value1";
        $value2 = (($value2 === "") || (is_null($value2))) ? ": " : ": $value2";
        switch (getMarker($node)) {
            case "unchanged":
                $string = "  " . getName($node) . $value1;
                break;
            case "changed":
                $string = "- " . getName($node) . $value1 . "\n" . str_repeat(" ", $indent) .
                    "+ " . getName($node) . $value2;
                break;
            case "added":
                $string = "+ " . getName($node) . $value2;
                break;
            case "deleted":
                $string = "- " . getName($node) . $value1;
                break;
            default:
                throw new \Exception("Unknown Marker: " . getMarker($node));
        }
        return str_repeat(" ", $indent) . $string;
    }, $diffTree);
    $result = flattenAll($result);
    return implode("\n", $result);
}

function getStylishFormat($diffTree): string
{
    $string = generateString($diffTree, 1);
    return "{\n$string\n}";
}
