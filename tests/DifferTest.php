<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ\Differ;

class DifferTest extends TestCase
{
    public function testGetDifference(): void
    {
        $path1 = __DIR__ . "/fixtures/file1.json";
        $path2 = __DIR__ . "/fixtures/file2.json";
        $result = file_get_contents(__DIR__ . "/fixtures/result.txt");
        $this->assertEquals($result, Differ\getDifference($path1, $path2));
        $path1 = __DIR__ . "/fixtures/file1.yml";
        $path2 = __DIR__ . "/fixtures/file2.yaml";
        $this->assertEquals($result, Differ\getDifference($path1, $path2));
    }
}
