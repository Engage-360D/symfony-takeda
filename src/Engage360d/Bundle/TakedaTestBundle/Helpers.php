<?php

namespace Engage360d\Bundle\TakedaTestBundle;

class Helpers
{
    public static function roundToNearest($value, $points) {
        $distances = array_map(function ($point) use ($value) {
            return array(
                "distance" => abs($point - $value),
                "point" => $point,
            );
        }, $points);
        usort($distances, function($a, $b) {
            return $a["distance"] - $b["distance"];
        });
        return $distances[0]["point"];
    }
    
    public static function roundToMax($value, $points)
    {
        sort($points);

        foreach ($points as $point) {
            $result = $point;
            if ($point > $value) {
                break;
            }
        }

        return $result;
    }
}
