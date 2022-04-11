<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ\Differ;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $path1 = __DIR__ . "/fixtures/file1.json";
        $path2 = __DIR__ . "/fixtures/file2.json";
        $format = 'stylish';
        $result = file_get_contents(__DIR__ . "/fixtures/resultStylish.txt");
        $this->assertEquals($result, Differ\genDiff($format, $path1, $path2));
        $format = 'plain';
        $result = file_get_contents(__DIR__ . "/fixtures/resultPlain.txt");
        $this->assertEquals($result, Differ\genDiff($format, $path1, $path2));
        $format = 'json';
        $result = file_get_contents(__DIR__ . "/fixtures/resultJson.txt");
        $this->assertEquals($result, Differ\genDiff($format, $path1, $path2));

        $path1 = __DIR__ . "/fixtures/file1.yml";
        $path2 = __DIR__ . "/fixtures/file2.yaml";
        $format = 'stylish';
        $result = file_get_contents(__DIR__ . "/fixtures/resultStylish.txt");
        $this->assertEquals($result, Differ\genDiff($format, $path1, $path2));
        $format = 'plain';
        $result = file_get_contents(__DIR__ . "/fixtures/resultPlain.txt");
        $this->assertEquals($result, Differ\genDiff($format, $path1, $path2));
        $format = 'json';
        $result = file_get_contents(__DIR__ . "/fixtures/resultJson.txt");
        $this->assertEquals($result, Differ\genDiff($format, $path1, $path2));
    }
}
