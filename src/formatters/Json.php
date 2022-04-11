<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\getName;
use function Differ\Differ\getValue1;
use function Differ\Differ\getValue2;
use function Differ\Differ\getMarker;
use function Differ\Differ\getChildren;

function getDiffArray($diffTree): array
{
    $acc = [];
    $diffTree = is_object($diffTree) ? get_object_vars($diffTree) : $diffTree;
    foreach ($diffTree as $node) {
        $marker = getMarker($node);
        $nodeName = getName($node);
        if (!empty(getChildren($node))) {
            $acc[] = ["marker" => $marker, $nodeName => getDiffArray(getChildren($node))];
        } else {
            switch ($marker) {
                case "unchanged":
                    $acc[] = ["marker" => $marker, $nodeName => getValue1($node)];
                    break;
                case "changed":
                    $acc[] = ["marker" => $marker, $nodeName => ["old" => getValue1($node), "new" => getValue2($node)]];
                    break;
                case "added":
                    $acc[] = ["marker" => $marker, $nodeName => getValue2($node)];
                    break;
                case "deleted":
                    $acc[] = ["marker" => $marker, $nodeName => getValue1($node)];
                    break;
                default:
                    throw new \Exception("Unknown Marker: " . $marker);
            }
        }
    }
    return $acc;
}
function getJsonFormat($diff)
{
    $string = json_encode(getDiffArray($diff));
    return "$string\n";
}
