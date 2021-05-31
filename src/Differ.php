<?php

namespace Differ\Differ;

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
function loadFile(string $filePath): string
{
    $fileContent = file_get_contents(getFilePath($filePath));
    if ($fileContent === false) {
        throw new \Exception("Error uploading a file'$filePath'");
    }
    return $fileContent;
}

function getStringValue($value)
{
    return is_bool($value) ? var_export($value, true) : $value;
}

function getDifference(string $filePath1, string $filePath2): string
{
    $file1 = loadFile($filePath1);
    $file2 = loadFile($filePath2);
    $parsedFile1 = json_decode($file1, true);
    $parsedFile2 = json_decode($file2, true);
    $commonFilesData = array_merge($parsedFile1, $parsedFile2);
    ksort($commonFilesData);
    $result = [];
    $commonKeys = array_keys($commonFilesData);
    foreach ($commonKeys as $key) {
        if (array_key_exists($key, $parsedFile1) && array_key_exists($key, $parsedFile2)) {
            $value1 = getStringValue($parsedFile1[$key]);
            $value2 = getStringValue($parsedFile2[$key]);
            if ($parsedFile1[$key] === $parsedFile2[$key]) {
                $result[] = "    $key: $value1";
            } else {
                $result[] = "  - $key: $value1";
                $result[] = "  + $key: $value2";
            }
        }
        if (array_key_exists($key, $parsedFile1) && !array_key_exists($key, $parsedFile2)) {
            $value1 = getStringValue($parsedFile1[$key]);
            $result[] = "  - $key: $value1";
        }
        if (!array_key_exists($key, $parsedFile1) && array_key_exists($key, $parsedFile2)) {
            $value2 = getStringValue($parsedFile2[$key]);
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
