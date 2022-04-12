<?php

namespace Differ\Differ;

use function Differ\Formatters\getFormat;
use function Differ\Parsers\parse;

function getFilePath(string $path): string
{
    $relativePath = getcwd() . '/' . $path;
    if (file_exists($path)) {
        return $path;
    } elseif (file_exists($relativePath)) {
        return $relativePath;
    } else {
        throw new \Exception("'$path' there is no such file");
    }
}
function loadFile(string $filePath): array
{
    $fileContent = file_get_contents(getFilePath($filePath));
    if ($fileContent === false) {
        throw new \Exception("Error uploading a file'$filePath'");
    }
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    return [$extension, $fileContent];
}

function getStringValue($value)
{
    if (is_bool($value)) {
        return var_export($value, true);
    } elseif (is_null($value)) {
        return strtolower(var_export($value, true));
    } else {
        return $value;
    }
}

function mkNode($name, $marker, $value1, $value2, $children = [])
{
    return [
        'name' => $name,
        'marker' => $marker,
        'value1' => $value1,
        'value2' => $value2,
        'children' => $children
    ];
}

function getName($node)
{
    return $node['name'];
}
function getValue1($node)
{
    return $node['value1'];
}
function getValue2($node)
{
    return $node['value2'];
}
function getMarker($node)
{
    return $node['marker'];
}
function getChildren($node)
{
    return $node['children'];
}

function repeat($tree1, $tree2): array
{
    $tree1 = get_object_vars($tree1);
    $tree2 = get_object_vars($tree2);
    $keys1 = array_keys($tree1);
    $keys2 = array_keys($tree2);
    $keys = array_unique(array_merge($keys1, $keys2));
    sort($keys);
    return array_map(function ($key) use ($tree1, $tree2) {
        if (array_key_exists($key, $tree1) && array_key_exists($key, $tree2)) {
            if (is_object($tree1[$key]) && is_object($tree2[$key])) {
                return mkNode($key, 'unchanged', null, null, repeat($tree1[$key], $tree2[$key]));
            }
            if ($tree1[$key] === $tree2[$key]) {
                return mkNode($key, 'unchanged', getStringValue($tree1[$key]), getStringValue($tree2[$key]));
            } else {
                return mkNode($key, 'changed', getStringValue($tree1[$key]), getStringValue($tree2[$key]));
            }
        }
        if (array_key_exists($key, $tree1) && !array_key_exists($key, $tree2)) {
            return mkNode($key, 'deleted', getStringValue($tree1[$key]), null);
        }
        if (!array_key_exists($key, $tree1) && array_key_exists($key, $tree2)) {
            return mkNode($key, 'added', null, getStringValue($tree2[$key]));
        }
    }, $keys);
}

function genDiff(string $filePath1, string $filePath2, string $format): string
{
    [$type1, $file1] = loadFile($filePath1);
    [$type2, $file2] = loadFile($filePath2);
    $parsedFile1 = parse($type1, $file1);
    $parsedFile2 = parse($type2, $file2);
    $diff = repeat($parsedFile1, $parsedFile2);
    return getFormat($diff, $format);
}

function run(string $filePath1, string $filePath2, string $format = 'stylish'): void
{
    print_r(genDiff($filePath1, $filePath2, $format));
}
