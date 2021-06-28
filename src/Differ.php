<?php

namespace Differ\Differ;

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
    return is_bool($value) ? var_export($value, true) : $value;
}

function getDifference(string $filePath1, string $filePath2): string
{
    [$type1, $file1] = loadFile($filePath1);
    [$type2, $file2] = loadFile($filePath2);
    $parsedFile1 = parse($type1, $file1);
    //var_dump($parsedFile1);
   // print_r($parsedFile1->host);
    //var_dump(get_object_vars($parsedFile1));
    $result1 = [];
    foreach ($parsedFile1 as $key => $value) {
        $result1[$key] = $value;
    }
    $parsedFile2 = parse($type2, $file2);
    $result2 = [];
    foreach ($parsedFile2 as $key => $value) {
        $result2[$key] = $value;
    }
    $commonFilesData = array_merge($result1, $result2);
    ksort($commonFilesData);
    $result = [];
    $commonKeys = array_keys($commonFilesData);
    foreach ($commonKeys as $key) {
        if (array_key_exists($key, $result1) && array_key_exists($key, $result2)) {
            $value1 = getStringValue($result1[$key]);
            $value2 = getStringValue($result2[$key]);
            if ($result1[$key] === $result2[$key]) {
                $result[] = "    $key: $value1";
            } else {
                $result[] = "  - $key: $value1";
                $result[] = "  + $key: $value2";
            }
        }
        if (array_key_exists($key, $result1) && !array_key_exists($key, $result2)) {
            $value1 = getStringValue($result1[$key]);
            $result[] = "  - $key: $value1";
        }
        if (!array_key_exists($key, $result1) && array_key_exists($key, $result2)) {
            $value2 = getStringValue($result2[$key]);
            $result[] = "  + $key: $value2";
        }
    }
    return implode("\n", $result);
}

function genDiff(string $filePath1, string $filePath2): void
{
    $output = getDifference($filePath1, $filePath2);
    print_r("{\n$output\n}\n");
}
